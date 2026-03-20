<?php

namespace App\Filament\Resources\RuiAgentis\Pages;

use App\Filament\Resources\RuiAgentis\RuiAgentisResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuiAgentis extends EditRecord
{
    protected static string $resource = RuiAgentisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
