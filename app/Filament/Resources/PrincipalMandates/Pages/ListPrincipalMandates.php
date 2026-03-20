<?php

namespace App\Filament\Resources\PrincipalMandates\Pages;

use App\Filament\Resources\PrincipalMandates\PrincipalMandateResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalMandates extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = PrincipalMandateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
