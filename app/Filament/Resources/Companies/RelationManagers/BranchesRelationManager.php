<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\CompanyBranches\Schemas\CompanyBranchForm;
use App\Filament\Resources\CompanyBranches\Tables\CompanyBranchesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'branches';
    protected static ?string $title = 'Sedi';

    public function form(Schema $schema): Schema
    {
        return CompanyBranchForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return CompanyBranchesTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
