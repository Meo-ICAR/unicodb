<?php

namespace App\Filament\Resources\EnasarcoLimits\Pages;

use App\Filament\Resources\EnasarcoLimits\EnasarcoLimitResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEnasarcoLimits extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = EnasarcoLimitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
