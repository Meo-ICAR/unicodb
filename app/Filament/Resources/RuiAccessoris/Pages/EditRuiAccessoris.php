<?php

namespace App\Filament\Resources\RuiAccessoris\Pages;

use App\Filament\Resources\RuiAccessoris\RuiAccessorisResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuiAccessoris extends EditRecord
{
    protected static string $resource = RuiAccessorisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
