<?php

namespace App\Filament\Resources\OamScopes;

use App\Filament\Resources\OamScopes\Pages\CreateOamScope;
use App\Filament\Resources\OamScopes\Pages\EditOamScope;
use App\Filament\Resources\OamScopes\Pages\ListOamScopes;
use App\Filament\Resources\OamScopes\Schemas\OamScopeForm;
use App\Filament\Resources\OamScopes\Tables\OamScopesTable;
use App\Models\OamScope;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class OamScopeResource extends Resource
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $model = OamScope::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';
    protected static ?string $modelLabel = 'OAM Prodotto';
    protected static ?string $pluralModelLabel = 'OAM Prodotti';
    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return OamScopeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OamScopesTable::configure($table);
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
            'index' => ListOamScopes::route('/'),
            'create' => CreateOamScope::route('/create'),
            'edit' => EditOamScope::route('/{record}/edit'),
        ];
    }
}
