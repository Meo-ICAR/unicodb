<?php

namespace App\Filament\Resources\ComplianceViolations\Pages;

use App\Filament\Resources\ComplianceViolations\ComplianceViolationResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListComplianceViolations extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ComplianceViolationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
