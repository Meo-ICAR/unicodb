<?php

namespace App\Filament\Resources\CompanyBranches\Pages;

use App\Filament\Resources\CompanyBranches\CompanyBranchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyBranch extends EditRecord
{
    protected static string $resource = CompanyBranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
