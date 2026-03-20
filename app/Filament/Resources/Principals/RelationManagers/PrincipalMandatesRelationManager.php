<?php

namespace App\Filament\Resources\Principals\RelationManagers;

use App\Filament\Resources\PrincipalMandates\Schemas\PrincipalMandateForm;
use App\Filament\Resources\PrincipalMandates\Tables\PrincipalMandatesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class PrincipalMandatesRelationManager extends RelationManager
{
    protected static string $relationship = 'mandates';

    public function form(Schema $schema): Schema
    {
        return PrincipalMandateForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return PrincipalMandatesTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
