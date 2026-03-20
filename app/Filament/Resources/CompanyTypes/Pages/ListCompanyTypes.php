<?php

namespace App\Filament\Resources\CompanyTypes\Pages;

use App\Filament\Resources\CompanyTypes\CompanyTypeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyTypes extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = CompanyTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
