<?php

namespace App\Filament\Resources\CompanyClients\Pages;

use App\Filament\Resources\CompanyClients\CompanyClientResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyClient extends EditRecord
{
    protected static string $resource = CompanyClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
