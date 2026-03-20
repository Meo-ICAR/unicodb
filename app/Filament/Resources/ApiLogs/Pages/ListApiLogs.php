<?php

namespace App\Filament\Resources\ApiLogs\Pages;

use App\Filament\Resources\ApiLogs\ApiLogResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApiLogs extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ApiLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
