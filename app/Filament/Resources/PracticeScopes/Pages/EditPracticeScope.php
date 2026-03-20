<?php

namespace App\Filament\Resources\PracticeScopes\Pages;

use App\Filament\Resources\PracticeScopes\PracticeScopeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPracticeScope extends EditRecord
{
    protected static string $resource = PracticeScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
