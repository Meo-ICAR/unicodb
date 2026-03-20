<?php

namespace App\Filament\Resources\VatMatchingResource\Pages;

use App\Filament\Resources\VatMatchingResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVatMatchings extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = VatMatchingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
