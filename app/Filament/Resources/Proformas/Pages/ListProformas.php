<?php

namespace App\Filament\Resources\Proformas\Pages;

use App\Filament\Resources\Proformas\ProformaResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProformas extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ProformaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
