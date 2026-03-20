<?php

namespace App\Filament\Resources\PracticeScopes;

use App\Filament\Resources\PracticeScopes\Pages\CreatePracticeScope;
use App\Filament\Resources\PracticeScopes\Pages\EditPracticeScope;
use App\Filament\Resources\PracticeScopes\Pages\ListPracticeScopes;
use App\Filament\Resources\PracticeScopes\Schemas\PracticeScopeForm;
use App\Filament\Resources\PracticeScopes\Tables\PracticeScopesTable;
use App\Models\PracticeScope;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PracticeScopeResource extends Resource
{
    protected static ?string $model = PracticeScope::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';


    public static function form(Schema $schema): Schema
    {
        return PracticeScopeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticeScopesTable::configure($table);
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
            'index' => ListPracticeScopes::route('/'),
            'create' => CreatePracticeScope::route('/create'),
            'edit' => EditPracticeScope::route('/{record}/edit'),
        ];
    }
}
