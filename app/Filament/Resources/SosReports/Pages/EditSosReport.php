<?php

namespace App\Filament\Resources\SosReports\Pages;

use App\Filament\Resources\SosReports\SosReportResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSosReport extends EditRecord
{
    protected static string $resource = SosReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
