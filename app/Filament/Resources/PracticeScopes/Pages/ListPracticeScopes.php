<?php

namespace App\Filament\Resources\PracticeScopes\Pages;

use App\Filament\Resources\PracticeScopes\PracticeScopeResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPracticeScopes extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = PracticeScopeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
