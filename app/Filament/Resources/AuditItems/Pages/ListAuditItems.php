<?php

namespace App\Filament\Resources\AuditItems\Pages;

use App\Filament\Resources\AuditItems\AuditItemResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAuditItems extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = AuditItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
