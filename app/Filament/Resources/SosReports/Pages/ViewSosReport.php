<?php

namespace App\Filament\Resources\SosReports\Pages;

use App\Filament\Resources\SosReports\SosReportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSosReport extends ViewRecord
{
    protected static string $resource = SosReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
