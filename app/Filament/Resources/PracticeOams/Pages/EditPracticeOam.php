<?php

namespace App\Filament\Resources\PracticeOams\Pages;

use App\Filament\Resources\PracticeOams\Schemas\PracticeOamForm;
use App\Filament\Resources\PracticeOams\PracticeOamResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPracticeOam extends EditRecord
{
    protected static string $resource = PracticeOamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
