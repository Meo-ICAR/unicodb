<?php

namespace App\Filament\Resources\SosReports\Pages;

use App\Filament\Resources\SosReports\SosReportResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSosReports extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = SosReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
