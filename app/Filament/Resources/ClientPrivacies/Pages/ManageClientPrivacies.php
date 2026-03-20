<?php

namespace App\Filament\Resources\ClientPrivacies\Pages;

use App\Filament\Resources\ClientPrivacies\ClientPrivacyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClientPrivacies extends ManageRecords
{
    protected static string $resource = ClientPrivacyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
