<?php

namespace App\Filament\Resources\PracticeStatuses\Pages;

use App\Filament\Resources\PracticeStatuses\PracticeStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPracticeStatus extends EditRecord
{
    protected static string $resource = PracticeStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
