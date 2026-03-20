<?php

namespace App\Filament\Resources\Remediations\Pages;

use App\Filament\Resources\Remediations\RemediationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRemediation extends EditRecord
{
    protected static string $resource = RemediationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
