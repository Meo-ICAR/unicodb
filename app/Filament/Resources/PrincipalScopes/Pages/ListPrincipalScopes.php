<?php

namespace App\Filament\Resources\PrincipalScopes\Pages;

use App\Filament\Resources\PrincipalScopes\PrincipalScopeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalScopes extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = PrincipalScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
