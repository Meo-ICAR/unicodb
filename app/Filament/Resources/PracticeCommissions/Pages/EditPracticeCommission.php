<?php

namespace App\Filament\Resources\PracticeCommissions\Pages;

use App\Filament\Resources\PracticeCommissions\PracticeCommissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPracticeCommission extends EditRecord
{
    protected static string $resource = PracticeCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
