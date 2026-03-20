<?php

namespace App\Filament\Resources\AuiRecords\Pages;

use App\Filament\Resources\AuiRecords\AuiRecordResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAuiRecords extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = AuiRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
