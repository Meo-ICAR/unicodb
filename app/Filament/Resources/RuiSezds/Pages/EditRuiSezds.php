<?php

namespace App\Filament\Resources\RuiSezds\Pages;

use App\Filament\Resources\RuiSezds\RuiSezdsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuiSezds extends EditRecord
{
    protected static string $resource = RuiSezdsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
