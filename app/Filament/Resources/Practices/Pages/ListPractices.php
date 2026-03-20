<?php

namespace App\Filament\Resources\Practices\Pages;

use App\Filament\Resources\Practices\PracticeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPractices extends ListRecords
{
    use HasRegolamentoAction;

    protected static string $resource = PracticeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
