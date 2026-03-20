<?php

namespace App\Filament\Resources\RuiCollaboratoris\Pages;

use App\Filament\Resources\RuiCollaboratoris\RuiCollaboratoriResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRuiCollaboratoris extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = RuiCollaboratoriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
