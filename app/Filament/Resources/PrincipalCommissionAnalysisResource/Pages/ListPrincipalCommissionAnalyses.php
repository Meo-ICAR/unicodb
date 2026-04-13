<?php

namespace App\Filament\Resources\PrincipalCommissionAnalysisResource\Pages;

use App\Filament\Resources\PrincipalCommissionAnalysisResource;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalCommissionAnalyses extends ListRecords
{
    protected static string $resource = PrincipalCommissionAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
