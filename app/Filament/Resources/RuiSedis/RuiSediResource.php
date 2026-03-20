<?php

namespace App\Filament\Resources\RuiSedis;

use App\Filament\Resources\RuiSedis\Pages\CreateRuiSedi;
use App\Filament\Resources\RuiSedis\Pages\EditRuiSedi;
use App\Filament\Resources\RuiSedis\Pages\ListRuiSedis;
use App\Filament\Resources\RuiSedis\Schemas\RuiSediForm;
use App\Filament\Resources\RuiSedis\Tables\RuiSedisTable;
use App\Models\RuiSedi;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RuiSediResource extends Resource
{
    protected static ?string $model = RuiSedi::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Sedi';
    protected static string|UnitEnum|null $navigationGroup = 'OAM-RUI';

    public static function form(Schema $schema): Schema
    {
        return RuiSediForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuiSedisTable::configure($table);
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
            'index' => ListRuiSedis::route('/'),
            'create' => CreateRuiSedi::route('/create'),
            'edit' => EditRuiSedi::route('/{record}/edit'),
        ];
    }
}
