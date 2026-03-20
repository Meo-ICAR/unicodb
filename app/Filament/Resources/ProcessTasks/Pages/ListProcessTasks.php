<?php

namespace App\Filament\Resources\ProcessTasks\Pages;

use App\Filament\Resources\ProcessTasks\ProcessTaskResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProcessTasks extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ProcessTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
