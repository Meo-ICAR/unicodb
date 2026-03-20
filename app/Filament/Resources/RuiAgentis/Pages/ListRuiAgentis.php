<?php

namespace App\Filament\Resources\RuiAgentis\Pages;

use App\Filament\Resources\RuiAgentis\RuiAgentisResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiAgentis extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = RuiAgentisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
