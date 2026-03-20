<?php

namespace App\Filament\Resources\ClientMandates\Pages;

use App\Filament\Resources\ClientMandates\ClientMandateResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientMandates extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = ClientMandateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
