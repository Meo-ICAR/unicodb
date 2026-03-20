<?php

namespace App\Filament\Resources\ProformaStatuses\Pages;

use App\Filament\Resources\ProformaStatuses\ProformaStatusResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProformaStatuses extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ProformaStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
