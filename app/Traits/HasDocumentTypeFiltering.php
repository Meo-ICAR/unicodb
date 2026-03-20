<?php

namespace App\Traits;

use App\Models\Agent;
use App\Models\Client;
use App\Models\DocumentType;
use App\Models\Practice;
use App\Models\Principal;

trait HasDocumentTypeFiltering
{
    /**
     * Get filtered document types based on record type
     */
    protected function getFilteredDocumentTypes($record): array
    {
        $targetType = match (true) {
            $record instanceof Agent => 'agent',
            $record instanceof Principal => 'principal',
            $record instanceof Client => 'client',
            $record instanceof Practice => 'practice',
            default => 'company'
        };

        return DocumentType::where("is_{$targetType}", true)
            ->orWhere('is_company', true)
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Get target type from record
     */
    protected function getTargetType($record): string
    {
        return match (true) {
            $record instanceof Agent => 'agent',
            $record instanceof Principal => 'principal',
            $record instanceof Client => 'client',
            $record instanceof Practice => 'practice',
            default => 'company'
        };
    }

    /**
     * Get document type options for select field
     */
    protected function getDocumentTypeOptions($record, ?callable $get = null): array
    {
        return $this->getFilteredDocumentTypes($record);
    }
}
