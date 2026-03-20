namespace App\Services;
<?php

use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Log;

class DocumentClassifier
{
    /**
     * Classifica un singolo documento basandosi sul nome del file o sul contenuto.
     */
    public function classify(Document $document, ?string $textContent = null): ?DocumentType
    {
        // Recuperiamo i tipi di documento ordinati per priorità
        $types = DocumentType::orderBy('priority', 'desc')->get();

        foreach ($types as $type) {
            if (!$type->regex)
                continue;

            // 1. Check sul nome del file (molto veloce)
            if (preg_match($type->regex, $document->name)) {
                return $type;
            }

            // 2. Check sul contenuto (se fornito ed estratto via OCR/PdfToText)
            if ($textContent && preg_match($type->regex, mb_substr($textContent, 0, 1000))) {
                return $type;
            }
        }

        return null;
    }
}
