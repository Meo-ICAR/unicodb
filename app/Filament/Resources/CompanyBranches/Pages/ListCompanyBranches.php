<?php

namespace App\Filament\Resources\CompanyBranches\Pages;

use App\Filament\Resources\CompanyBranches\CompanyBranchResource;
use App\Filament\Traits\HasRegolamentoAction;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyBranches extends ListRecords
{
    use HasRegolamentoAction;
    
    protected static string $resource = CompanyBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->getRegolamentoAction(),
        ];
    }
}
