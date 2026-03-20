<?php

namespace App\Filament\Resources\CompanyClients\Pages;

use App\Filament\Resources\CompanyClients\CompanyClientResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyClients extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = CompanyClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
