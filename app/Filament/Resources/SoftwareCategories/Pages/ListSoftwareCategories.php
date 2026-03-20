<?php

namespace App\Filament\Resources\SoftwareCategories\Pages;

use App\Filament\Resources\SoftwareCategories\SoftwareCategoryResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSoftwareCategories extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = SoftwareCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
