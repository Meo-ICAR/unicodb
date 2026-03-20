<?php

namespace App\Filament\Resources\Firrs\Pages;

use App\Filament\Resources\Firrs\FirrResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFirrs extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = FirrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
