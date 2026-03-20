<?php

namespace App\Filament\Resources\EnasarcoLimits\Pages;

use App\Filament\Resources\EnasarcoLimits\EnasarcoLimitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEnasarcoLimit extends EditRecord
{
    protected static string $resource = EnasarcoLimitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
