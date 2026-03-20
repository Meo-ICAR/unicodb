<?php

namespace App\Filament\Resources\Abis\Pages;

use App\Filament\Resources\Abis\AbiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAbi extends EditRecord
{
    protected static string $resource = AbiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
