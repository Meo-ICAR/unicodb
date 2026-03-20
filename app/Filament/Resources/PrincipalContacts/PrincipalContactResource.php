<?php

namespace App\Filament\Resources\PrincipalContacts;

use App\Filament\Resources\PrincipalContacts\Pages\CreatePrincipalContact;
use App\Filament\Resources\PrincipalContacts\Pages\EditPrincipalContact;
use App\Filament\Resources\PrincipalContacts\Pages\ListPrincipalContacts;
use App\Filament\Resources\PrincipalContacts\Schemas\PrincipalContactForm;
use App\Filament\Resources\PrincipalContacts\Tables\PrincipalContactsTable;
use App\Models\PrincipalContact;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PrincipalContactResource extends Resource
{
    protected static bool $isScopedToTenant = false;
    protected static ?string $model = PrincipalContact::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    public static function form(Schema $schema): Schema
    {
        return PrincipalContactForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrincipalContactsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrincipalContacts::route('/'),
            'create' => CreatePrincipalContact::route('/create'),
            'edit' => EditPrincipalContact::route('/{record}/edit'),
        ];
    }
}
