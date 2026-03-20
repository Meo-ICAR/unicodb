<?php

namespace App\Filament\Resources\ClientRelations\Pages;

use App\Filament\Resources\ClientRelations\ClientRelationResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientRelations extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ClientRelationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
