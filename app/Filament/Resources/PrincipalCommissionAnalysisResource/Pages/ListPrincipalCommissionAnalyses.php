<?php

namespace App\Filament\Resources\PrincipalCommissionAnalysisResource\Pages;

use App\Filament\Resources\PrincipalCommissionAnalysisResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalCommissionAnalyses extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = PrincipalCommissionAnalysisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
