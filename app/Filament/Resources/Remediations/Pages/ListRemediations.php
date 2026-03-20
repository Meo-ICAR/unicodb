<?php

namespace App\Filament\Resources\Remediations\Pages;

use App\Filament\Resources\Remediations\RemediationResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRemediations extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = RemediationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
