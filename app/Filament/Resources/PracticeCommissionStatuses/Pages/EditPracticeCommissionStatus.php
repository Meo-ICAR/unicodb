<?php

namespace App\Filament\Resources\PracticeCommissionStatuses\Pages;

use App\Filament\Resources\PracticeCommissionStatuses\PracticeCommissionStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPracticeCommissionStatus extends EditRecord
{
    protected static string $resource = PracticeCommissionStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
