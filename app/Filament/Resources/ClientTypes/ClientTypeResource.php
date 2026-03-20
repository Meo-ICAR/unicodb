<?php

namespace App\Filament\Resources\ClientTypes;

use App\Filament\Resources\ClientTypes\Pages\CreateClientType;
use App\Filament\Resources\ClientTypes\Pages\EditClientType;
use App\Filament\Resources\ClientTypes\Pages\ListClientTypes;
use App\Filament\Resources\ClientTypes\Schemas\ClientTypeForm;
use App\Filament\Resources\ClientTypes\Tables\ClientTypesTable;
use App\Models\ClientType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ClientTypeResource extends Resource
{
    protected static ?string $model = ClientType::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';


    public static function form(Schema $schema): Schema
    {
        return ClientTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientTypesTable::configure($table);
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
            'index' => ListClientTypes::route('/'),
            'create' => CreateClientType::route('/create'),
            'edit' => EditClientType::route('/{record}/edit'),
        ];
    }
}
