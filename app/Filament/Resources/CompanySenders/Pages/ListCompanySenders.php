<?php

namespace App\Filament\Resources\CompanySenders\Pages;

use App\Filament\Resources\CompanySenders\CompanySenderResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanySenders extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = CompanySenderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
