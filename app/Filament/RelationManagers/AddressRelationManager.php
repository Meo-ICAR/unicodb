<?php

namespace App\Filament\RelationManagers;

use App\Filament\Resources\Addresses\Schemas\AddressForm;
use App\Filament\Resources\Addresses\Tables\AddressesTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Tables;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    protected static ?string $title = 'Indirizzo';

    public function form(Schema $schema): Schema
    {
        return AddressForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return AddressesTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make()
                    ->label('Nuovo Indirizzo'),
            ])
            ->recordActions([
                Tables\Actions\EditAction::make()
                    ->label('Modifica'),
                Tables\Actions\DeleteAction::make()
                    ->label('Elimina'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Elimina Selezionati'),
                ]),
            ]);
    }
}
