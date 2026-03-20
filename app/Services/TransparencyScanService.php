<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class TransparencyScanService
{
    /**
     * Scan transparency links for a specific company
     *
     * @param string|null $companyId Company ID to filter websites
     * @param int $limit Maximum number of websites to scan
     * @return array Scan results
     */
    public function scanForCompany(?string $companyId = null, int $limit = 100): array
    {
        $results = [
            'total_websites' => 0,
            'processed_websites' => 0,
            'found_transparency_pages' => 0,
            'extracted_documents' => 0,
            'errors' => [],
            'details' => []
        ];

        try {
            // Get websites that have transparency_date but no url_trasparency
            $query = Website::where('company_id', $companyId)->whereNotNull('url_transparency');

            $websites = $query->limit($limit)->get();
            $results['total_websites'] = $websites->count();

            Log::info("Starting transparency scan for {$results['total_websites']} websites");

            foreach ($websites as $website) {
                $websiteResult = $this->processWebsite($website);
                $results['processed_websites']++;

                if ($websiteResult['found_transparency_page']) {
                    $results['found_transparency_pages']++;
                }

                $results['extracted_documents'] += $websiteResult['documents_created'];
                $results['details'][] = $websiteResult;

                if (!empty($websiteResult['errors'])) {
                    $results['errors'] = array_merge($results['errors'], $websiteResult['errors']);
                }
            }

            Log::info("Transparency scan completed: {$results['found_transparency_pages']} transparency pages found, "
                . "{$results['extracted_documents']} documents extracted");
        } catch (\Exception $e) {
            Log::error('Transparency scan failed: ' . $e->getMessage());
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Scan a single website
     *
     * @param Website $website
     * @return array Processing results
     */
    public function scanSingleWebsite(Website $website): array
    {
        return $this->processWebsite($website);
    }

    /**
     * Process individual website and extract transparency links
     */
    private function processWebsite(Website $website): array
    {
        $result = [
            'website_id' => $website->id,
            'domain' => $website->domain,
            'company_id' => $website->company_id,
            'found_transparency_page' => false,
            'transparency_urls' => [],
            'documents_created' => 0,
            'errors' => [],
            'details' => []
        ];

        try {
            // Try to find transparency page
            $transparencyUrls = $this->findTransparencyUrls($website->domain);

            if (empty($transparencyUrls)) {
                $result['details'][] = "No transparency page found for {$website->domain}";
                return $result;
            }

            $result['found_transparency_page'] = true;
            $result['transparency_urls'] = $transparencyUrls;

            foreach ($transparencyUrls as $transparencyUrl) {
                $result['details'][] = "Found transparency page: {$transparencyUrl}";

                // Extract links from the transparency page
                $links = $this->extractLinksFromPage($transparencyUrl);

                if (empty($links)) {
                    $result['details'][] = 'No links found on transparency page';
                    continue;
                }

                $result['details'][] = 'Found ' . count($links) . ' links on transparency page';

                // Create document records for each link
                foreach ($links as $link) {
                    $documentResult = $this->createDocumentFromLink($link, $website, $transparencyUrl);

                    if ($documentResult['success']) {
                        if (isset($documentResult['details']) && str_contains($documentResult['details'][0] ?? '', 'already exists')) {
                            $result['details'][] = "✓ Document already exists: {$documentResult['document_name']}";
                        } else {
                            $result['documents_created']++;
                            $result['details'][] = "✓ Created document: {$documentResult['document_name']} (ID: {$documentResult['document_id']})";
                        }
                    } else {
                        $result['errors'][] = "Failed to create document for {$link}: {$documentResult['error']}";
                    }
                }

                // Update the website with the first transparency URL found
                if (!$website->url_transparency) {
                    $website->update(['url_transparency' => $transparencyUrl]);
                    $result['details'][] = 'Updated website with transparency URL';
                }
            }
        } catch (\Exception $e) {
            $result['errors'][] = "Error processing website {$website->domain}: " . $e->getMessage();
            Log::error("Error processing website {$website->domain}: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Create a Document record from a link
     */
    private function createDocumentFromLink(string $link, Website $website, string $transparencyUrl): array
    {
        $result = [
            'success' => false,
            'document_id' => null,
            'document_name' => null,
            'error' => null
        ];

        try {
            // Get or create a document type for transparency documents
            $documentType = $this->getOrCreateTransparencyDocumentType();

            // Extract filename from URL
            $filename = basename(parse_url($link, PHP_URL_PATH));
            $name = $filename ?: 'Transparency Document';

            // Extract date from wp-content/uploads URL
            $emittedAt = $this->extractDateFromUrl($link) ?? $website->transparency_date;

            // Create document record
            // Only create if url_document is not present for this company
            $existingDocument = Document::where('company_id', $website->company_id)
                ->where('url_document', $link)
                ->first();

            if ($existingDocument) {
                $result['success'] = true;
                $result['document_id'] = $existingDocument->id;
                $result['document_name'] = $existingDocument->name;
                $result['details'][] = "Document already exists for {$link}";
                return $result;
            }

            $document = Document::create([
                'company_id' => $website->company_id,
                'documentable_id' => $website->company_id,
                'documentable_type' => Company::class,
                'document_type_id' => $documentType->id,
                'name' => $name,
                'description' => "Document found on transparency page: {$transparencyUrl}",
                'status' => 'uploaded',
                'url_document' => $link,  // Store the URL in the url_document field
                'emitted_at' => $emittedAt,
                'uploaded_by' => 1,  // System user ID, adjust as needed
            ]);

            $result['success'] = true;
            $result['document_id'] = $document->id;
            $result['document_name'] = $document->name;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            Log::error("Failed to create document for {$link}: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Extract year and month from wp-content/uploads URL and return Carbon date
     */
    private function extractDateFromUrl(string $url): ?Carbon
    {
        // Check if URL contains wp-content/uploads
        if (strpos($url, 'wp-content/uploads') === false) {
            return null;
        }

        // Extract year and month from URL pattern: /wp-content/uploads/YYYY/MM/filename
        $pattern = '/wp-content\/uploads\/(\d{4})\/(\d{2})\//';

        if (preg_match($pattern, $url, $matches)) {
            $year = $matches[1];
            $month = $matches[2];

            // Create Carbon date with first day of the month
            try {
                return Carbon::create($year, $month, 1);
            } catch (\Exception $e) {
                return null;
            }
        }

        return null;
    }

    /**
     * Get or create a document type for transparency documents
     */
    private function getOrCreateTransparencyDocumentType(): DocumentType
    {
        $documentType = DocumentType::where('code', 'TRANSPARENCY_DOC')->first();

        if (!$documentType) {
            $documentType = DocumentType::create([
                'name' => 'Transparency Document',
                'code' => 'TRANSPARENCY_DOC',
                'is_person' => false,
                'is_signed' => false,
                'is_stored' => true,
                'is_practice' => false,
                'is_monitored' => true,
                'is_template' => false,
                'duration' => 365,  // 1 year validity
                'is_sensible' => false,
            ]);

            Log::info('Created document type: Transparency Document');
        }

        return $documentType;
    }

    /**
     * Find possible transparency URLs for a domain
     */
    private function findTransparencyUrls(string $domain): array
    {
        $urls = [];

        // Ensure domain has protocol
        if (!str_starts_with($domain, 'http')) {
            $domain = 'https://' . $domain;
        }

        // Common transparency page paths
        $paths = [
            '/trasparenza',
            '/trasparenza/',
            '/transparency',
            '/transparency/',
            '/amministrazione-trasparente',
            '/amministrazione-trasparente/',
            '/privacy',
            '/privacy/',
            '/informative',
            '/informative/',
            '/legal',
            '/legal/',
            '/legal/privacy',
            '/legal/privacy/',
            '/informativa-privacy',
            '/informativa-privacy/',
            '/cookie-policy',
            '/cookie-policy/',
            '/footer',
            '/footer/',
        ];

        foreach ($paths as $path) {
            $url = rtrim($domain, '/') . $path;

            if ($this->urlExists($url)) {
                $urls[] = $url;
            }
        }

        return $urls;
    }

    /**
     * Check if URL exists and is accessible
     */
    private function urlExists(string $url): bool
    {
        try {
            $response = Http::timeout(10)->get($url);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Extract all links from a web page
     */
    private function extractLinksFromPage(string $url): array
    {
        try {
            $response = Http::timeout(15)->get($url);

            if (!$response->successful()) {
                return [];
            }

            $html = $response->body();
            $crawler = new Crawler($html);

            $links = [];

            // Extract all href attributes from anchor tags
            $crawler->filter('a[href]')->each(function (Crawler $node) use (&$links, $url) {
                $href = $node->attr('href');

                // Skip empty, javascript, mailto, tel links
                if (empty($href) ||
                        str_starts_with($href, 'javascript:') ||
                        str_starts_with($href, 'mailto:') ||
                        str_starts_with($href, 'tel:')) {
                    return;
                }

                // Convert relative URLs to absolute
                $absoluteUrl = $this->makeAbsoluteUrl($href, $url);

                // Only include links that point to files (common document extensions)
                if ($this->isFileLink($absoluteUrl)) {
                    $links[] = $absoluteUrl;
                }
            });

            // Remove duplicates and return
            return array_unique($links);
        } catch (\Exception $e) {
            Log::error("Error extracting links from {$url}: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Convert relative URL to absolute URL
     */
    private function makeAbsoluteUrl(string $href, string $baseUrl): string
    {
        // If already absolute, return as is
        if (str_starts_with($href, 'http')) {
            return $href;
        }

        $baseParts = parse_url($baseUrl);
        $scheme = $baseParts['scheme'] ?? 'https';
        $host = $baseParts['host'] ?? '';
        $port = isset($baseParts['port']) ? ':' . $baseParts['port'] : '';

        $base = $scheme . '://' . $host . $port;

        // Handle root-relative URLs
        if (str_starts_with($href, '/')) {
            return $base . $href;
        }

        // Handle relative URLs
        $path = dirname($baseParts['path'] ?? '/');
        return $base . rtrim($path, '/') . '/' . ltrim($href, '/');
    }

    /**
     * Check if URL points to a file (document)
     */
    private function isFileLink(string $url): bool
    {
        $fileExtensions = [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'txt', 'rtf', 'odt', 'ods', 'odp', 'zip', 'rar',
            'jpg', 'jpeg', 'png', 'gif', 'svg', 'bmp'
        ];

        $path = parse_url($url, PHP_URL_PATH);
        if (!$path) {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, $fileExtensions);
    }

    /**
     * Get scan statistics for a company
     */
    public function getCompanyScanStats(string $companyId): array
    {
        $totalWebsites = Website::where('company_id', $companyId)
            ->whereNotNull('transparency_date')
            ->count();

        $scannedWebsites = Website::where('company_id', $companyId)
            ->whereNotNull('transparency_date')
            ->whereNotNull('url_transparency')
            ->where('url_transparency', '!=', '')
            ->count();

        $documentsCount = Document::where('company_id', $companyId)
            ->whereHas('documentType', function ($query) {
                $query->where('code', 'TRANSPARENCY_DOC');
            })
            ->count();

        return [
            'total_websites_with_transparency_date' => $totalWebsites,
            'scanned_websites' => $scannedWebsites,
            'pending_websites' => $totalWebsites - $scannedWebsites,
            'extracted_documents' => $documentsCount,
        ];
    }
}
