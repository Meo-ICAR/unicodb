<?php

namespace App\Filament\Resources\BusinessFunctions;

use App\Filament\Resources\BusinessFunctions\Pages\CreateBusinessFunction;
use App\Filament\Resources\BusinessFunctions\Pages\EditBusinessFunction;
use App\Filament\Resources\BusinessFunctions\Pages\ListBusinessFunctions;
use App\Filament\Resources\BusinessFunctions\Schemas\BusinessFunctionForm;
use App\Filament\Resources\BusinessFunctions\Tables\BusinessFunctionsTable;
use App\Models\BusinessFunction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class BusinessFunctionResource extends Resource
{
    protected static ?string $model = BusinessFunction::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Funzioni aziendali';

    protected static ?string $modelLabel = 'Funzione aziendale';

    protected static ?string $pluralModelLabel = 'Funzioni aziendali';

    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';

    //  protected static string|BackedEnum|null $navigationGroup = 'Impostazioni';

    public static function form(Schema $schema): Schema
    {
        return BusinessFunctionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusinessFunctionsTable::configure($table);
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
            'index' => ListBusinessFunctions::route('/'),
            'create' => CreateBusinessFunction::route('/create'),
            'edit' => EditBusinessFunction::route('/{record}/edit'),
        ];
    }
}
