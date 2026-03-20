<?php

namespace App\Filament\Resources\RuiCariches\Pages;

use App\Filament\Resources\RuiCariches\RuiCaricheResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuiCariche extends EditRecord
{
    protected static string $resource = RuiCaricheResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
