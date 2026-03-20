<?php

namespace App\Filament\Resources\RuiSedis\Pages;

use App\Filament\Resources\RuiSedis\RuiSediResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRuiSedi extends EditRecord
{
    protected static string $resource = RuiSediResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
