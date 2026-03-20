<?php

namespace App\Filament\Resources\SoftwareMappings\Pages;

use App\Filament\Resources\SoftwareMappings\SoftwareMappingResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSoftwareMappings extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = SoftwareMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
