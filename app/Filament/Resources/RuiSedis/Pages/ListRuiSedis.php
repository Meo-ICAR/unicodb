<?php

namespace App\Filament\Resources\RuiSedis\Pages;

use App\Filament\Resources\RuiSedis\RuiSediResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiSedis extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = RuiSediResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
