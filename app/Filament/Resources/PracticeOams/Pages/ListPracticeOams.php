<?php

namespace App\Filament\Resources\PracticeOams\Pages;

use App\Filament\Resources\PracticeOams\PracticeOamResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticeOams extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = PracticeOamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
