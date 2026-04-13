<?php

namespace App\Filament\Resources\VatMatchingResource\Pages;

use App\Filament\Resources\VatMatchingResource;
use Filament\Resources\Pages\ListRecords;

class ListVatMatchings extends ListRecords
{
    protected static string $resource = VatMatchingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
