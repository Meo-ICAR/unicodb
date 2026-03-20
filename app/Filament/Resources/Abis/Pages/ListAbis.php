<?php

namespace App\Filament\Resources\Abis\Pages;

use App\Filament\Resources\Abis\AbiResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAbis extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = AbiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
