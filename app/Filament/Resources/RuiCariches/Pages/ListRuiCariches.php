<?php

namespace App\Filament\Resources\RuiCariches\Pages;

use App\Filament\Resources\RuiCariches\RuiCaricheResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiCariches extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = RuiCaricheResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
