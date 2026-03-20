<?php

namespace App\Filament\Resources\PrincipalScopes;

use App\Filament\Resources\PrincipalScopes\Pages\CreatePrincipalScope;
use App\Filament\Resources\PrincipalScopes\Pages\EditPrincipalScope;
use App\Filament\Resources\PrincipalScopes\Pages\ListPrincipalScopes;
use App\Filament\Resources\PrincipalScopes\Schemas\PrincipalScopeForm;
use App\Filament\Resources\PrincipalScopes\Tables\PrincipalScopesTable;
use App\Models\PrincipalScope;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PrincipalScopeResource extends Resource
{
    protected static ?string $model = PrincipalScope::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return PrincipalScopeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrincipalScopesTable::configure($table);
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
            'index' => ListPrincipalScopes::route('/'),
            'create' => CreatePrincipalScope::route('/create'),
            'edit' => EditPrincipalScope::route('/{record}/edit'),
        ];
    }
}
