<?php

namespace App\Filament\Resources\CompanyFunctions\Pages;

use App\Filament\Resources\CompanyFunctions\CompanyFunctionResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyFunctions extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = CompanyFunctionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
