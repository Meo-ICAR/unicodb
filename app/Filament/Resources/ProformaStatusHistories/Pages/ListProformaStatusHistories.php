<?php

namespace App\Filament\Resources\ProformaStatusHistories\Pages;

use App\Filament\Resources\ProformaStatusHistories\ProformaStatusHistoryResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProformaStatusHistories extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ProformaStatusHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
