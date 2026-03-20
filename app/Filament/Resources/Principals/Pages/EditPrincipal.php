<?php

namespace App\Filament\Resources\Principals\Pages;

use App\Filament\Resources\Principals\PrincipalResource;
use App\Services\ChecklistService;
use Filament\Actions\DeleteAction;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPrincipal extends EditRecord
{
    protected static string $resource = PrincipalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
