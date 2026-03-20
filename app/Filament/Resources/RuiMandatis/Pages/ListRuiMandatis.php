<?php

namespace App\Filament\Resources\RuiMandatis\Pages;

use App\Filament\Resources\RuiMandatis\RuiMandatiResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiMandatis extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = RuiMandatiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
