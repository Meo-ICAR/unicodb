<?php

namespace App\Filament\Resources\PrincipalContacts\Pages;

use App\Filament\Resources\PrincipalContacts\PrincipalContactResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalContacts extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = PrincipalContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
