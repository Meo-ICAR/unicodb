<?php

namespace App\Filament\Resources\PrincipalEmployees\Pages;

use App\Filament\Resources\PrincipalEmployees\PrincipalEmployeeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrincipalEmployees extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = PrincipalEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
