<?php

namespace App\Filament\Resources\ClientMandates\Pages;

use App\Filament\Resources\ClientMandates\ClientMandateResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditClientMandate extends EditRecord
{
    protected static string $resource = ClientMandateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
