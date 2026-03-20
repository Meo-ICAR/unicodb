<?php

namespace App\Services;

use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class DocumentClassificationService
{
    /**
     * Classifica documents using regex patterns from document_types table
     *
     * @param int|null $limit Maximum number of documents to process
     * @param bool $verbose Whether to return detailed results
     * @return array Classification results
     */
    public function classifyDocuments(?int $limit = null, bool $verbose = false): array
    {
        $results = [
            'total_documents' => 0,
            'processed_documents' => 0,
            'classified_documents' => 0,
            'unclassified_documents' => 0,
            'errors' => [],
            'details' => []
        ];

        try {
            // 1. Get all document types with regex defined, ordered by priority
            $types = DocumentType::whereNotNull('regex')
                ->orderBy('priority', 'desc')
                ->get();

            if ($types->isEmpty()) {
                $results['errors'][] = 'Nessuna regex trovata nella tabella document_types. Hai lanciato il seeder?';
                return $results;
            }

            // 2. Get documents to classify (those without document_type_id)
            $query = Document::whereNull('document_type_id');

            if ($limit) {
                $query->limit($limit);
            }

            $documents = $query->get();
            $results['total_documents'] = $documents->count();

            Log::info("Starting document classification for {$results['total_documents']} documents");

            foreach ($documents as $doc) {
                $documentResult = $this->processDocument($doc, $types);
                $results['processed_documents']++;

                if ($documentResult['classified']) {
                    $results['classified_documents']++;
                } else {
                    $results['unclassified_documents']++;
                }

                if ($verbose) {
                    $results['details'][] = $documentResult;
                }
            }

            Log::info("Document classification completed. Classified: {$results['classified_documents']}, Unclassified: {$results['unclassified_documents']}");
        } catch (\Exception $e) {
            $results['errors'][] = 'Errore durante la classificazione: ' . $e->getMessage();
            Log::error('Document classification error: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Process a single document and try to classify it
     *
     * @param Document $document
     * @param Collection $types
     * @return array Processing result
     */
    private function processDocument(Document $document, Collection $types): array
    {
        $result = [
            'document_id' => $document->id,
            'document_name' => $document->name,
            'classified' => false,
            'matched_type' => null,
            'error' => null
        ];

        try {
            foreach ($types as $type) {
                // Check regex against document name
                if (preg_match($type->regex, $document->name)) {
                    $document->document_type_id = $type->id;
                    if ($document->save()) {
                        Log::info("Document {$document->id} classified as type {$type->name}");
                        if (!empty($document->emitted_at) && empty($document->expires_at)) {
                            $gg = $document->documentType->duration;
                            if (!empty($gg) && ($gg > 0)) {
                                $document->expires_at = $document->emitted_at->addDays($gg);
                                if ($document->documentType->is_endmonth) {
                                    $document->expires_at = $document->expires_at->endOfMonth();
                                }
                            }
                            $document->save();
                        }
                    }

                    $result['classified'] = true;
                    $result['matched_type'] = [
                        'id' => $type->id,
                        'name' => $type->name,
                        'regex' => $type->regex
                    ];

                    Log::info("Document {$document->id} classified as type {$type->name}");
                    break;  // Found match, move to next document
                }
            }

            if (!$result['classified']) {
                Log::info("Document {$document->id} could not be classified");
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            Log::error("Error processing document {$document->id}: " . $e->getMessage());
        }

        return $result;
    }

    /**
     * Classify a single document
     *
     * @param Document $document
     * @return bool True if classified, false otherwise
     */
    public function classifySingleDocument(Document $document): bool
    {
        $types = DocumentType::whereNotNull('regex')
            ->orderBy('priority', 'desc')
            ->get();

        if ($types->isEmpty()) {
            Log::warning('No regex patterns found in document_types table');
            return false;
        }

        $result = $this->processDocument($document, $types);
        return $result['classified'];
    }

    /**
     * Get statistics about document classification
     *
     * @return array Statistics
     */
    public function getClassificationStats(): array
    {
        $total = Document::count();
        $classified = Document::whereNotNull('document_type_id')->count();
        $unclassified = $total - $classified;
        $typesWithRegex = DocumentType::whereNotNull('regex')->count();

        return [
            'total_documents' => $total,
            'classified_documents' => $classified,
            'unclassified_documents' => $unclassified,
            'classification_percentage' => $total > 0 ? round(($classified / $total) * 100, 2) : 0,
            'types_with_regex' => $typesWithRegex
        ];
    }
}
