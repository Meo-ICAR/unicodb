<?php

namespace App\Filament\Resources\ClientRelations\Pages;

use App\Filament\Resources\ClientRelations\ClientRelationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClientRelation extends EditRecord
{
    protected static string $resource = ClientRelationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
