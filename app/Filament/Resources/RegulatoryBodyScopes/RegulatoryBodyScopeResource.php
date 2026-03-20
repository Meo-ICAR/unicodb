<?php

namespace App\Filament\Resources\RegulatoryBodyScopes;

use App\Filament\Resources\RegulatoryBodyScopes\Pages\CreateRegulatoryBodyScope;
use App\Filament\Resources\RegulatoryBodyScopes\Pages\EditRegulatoryBodyScope;
use App\Filament\Resources\RegulatoryBodyScopes\Pages\ListRegulatoryBodyScopes;
use App\Filament\Resources\RegulatoryBodyScopes\Schemas\RegulatoryBodyScopeForm;
use App\Filament\Resources\RegulatoryBodyScopes\Tables\RegulatoryBodyScopesTable;
use App\Models\RegulatoryBodyScope;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RegulatoryBodyScopeResource extends Resource
{
    protected static ?string $model = RegulatoryBodyScope::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    public static function form(Schema $schema): Schema
    {
        return RegulatoryBodyScopeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegulatoryBodyScopesTable::configure($table);
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
            'index' => ListRegulatoryBodyScopes::route('/'),
            'create' => CreateRegulatoryBodyScope::route('/create'),
            'edit' => EditRegulatoryBodyScope::route('/{record}/edit'),
        ];
    }
}
