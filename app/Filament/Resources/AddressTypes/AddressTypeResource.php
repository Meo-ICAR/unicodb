<?php

namespace App\Filament\Resources\AddressTypes;

use App\Filament\Resources\AddressTypes\Pages\CreateAddressType;
use App\Filament\Resources\AddressTypes\Pages\EditAddressType;
use App\Filament\Resources\AddressTypes\Pages\ListAddressTypes;
use App\Filament\Resources\AddressTypes\Schemas\AddressTypeForm;
use App\Filament\Resources\AddressTypes\Tables\AddressTypesTable;
use App\Models\AddressType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AddressTypeResource extends Resource
{
    protected static ?string $model = AddressType::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';


    public static function form(Schema $schema): Schema
    {
        return AddressTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AddressTypesTable::configure($table);
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
            'index' => ListAddressTypes::route('/'),
            'create' => CreateAddressType::route('/create'),
            'edit' => EditAddressType::route('/{record}/edit'),
        ];
    }
}
