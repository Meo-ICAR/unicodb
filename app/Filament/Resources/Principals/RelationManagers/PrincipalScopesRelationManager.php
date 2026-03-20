<?php

namespace App\Filament\Resources\Principals\RelationManagers;

use App\Filament\Resources\PrincipalScopes\Schemas\PrincipalScopeForm;
use App\Filament\Resources\PrincipalScopes\Tables\PrincipalScopesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class PrincipalScopesRelationManager extends RelationManager
{
    protected static string $relationship = 'principalScopes';

    protected static ?string $title = 'Ambiti Operativi';

    public function form(Schema $schema): Schema
    {
        return PrincipalScopeForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return PrincipalScopesTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
