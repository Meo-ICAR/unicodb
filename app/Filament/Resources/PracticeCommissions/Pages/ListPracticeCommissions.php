<?php

namespace App\Filament\Resources\PracticeCommissions\Pages;

use App\Filament\Resources\PracticeCommissions\PracticeCommissionResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticeCommissions extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = PracticeCommissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
