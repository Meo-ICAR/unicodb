<?php

namespace App\Filament\Resources\RuiWebSites\Pages;

use App\Filament\Resources\RuiWebSites\RuiWebSitesResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuiWebSites extends EditRecord
{
    protected static string $resource = RuiWebSitesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
