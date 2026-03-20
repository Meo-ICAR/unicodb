<?php

namespace App\Filament\Resources\TrainingTemplates\Pages;

use App\Filament\Resources\TrainingTemplates\TrainingTemplateResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainingTemplate extends EditRecord
{
    protected static string $resource = TrainingTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
