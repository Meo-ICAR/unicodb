<?php

namespace App\Filament\Resources\Vcoges\Pages;

use App\Filament\Resources\Vcoges\VcogesResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVcoges extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = VcogesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
