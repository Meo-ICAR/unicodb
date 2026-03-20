<?php

namespace App\Filament\Resources\Principals\RelationManagers;

use App\Filament\Resources\PrincipalContacts\Schemas\PrincipalContactForm;
use App\Filament\Resources\PrincipalContacts\Tables\PrincipalContactsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public function form(Schema $schema): Schema
    {
        return PrincipalContactForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return PrincipalContactsTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
