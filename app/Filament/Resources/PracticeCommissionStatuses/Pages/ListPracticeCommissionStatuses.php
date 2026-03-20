<?php

namespace App\Filament\Resources\PracticeCommissionStatuses\Pages;

use App\Filament\Resources\PracticeCommissionStatuses\PracticeCommissionStatusResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticeCommissionStatuses extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = PracticeCommissionStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
