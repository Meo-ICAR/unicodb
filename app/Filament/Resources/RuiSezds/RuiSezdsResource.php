<?php

namespace App\Filament\Resources\RuiSezds;

use App\Filament\Resources\RuiSezds\Pages\CreateRuiSezds;
use App\Filament\Resources\RuiSezds\Pages\EditRuiSezds;
use App\Filament\Resources\RuiSezds\Pages\ListRuiSezds;
use App\Filament\Resources\RuiSezds\Schemas\RuiSezdsForm;
use App\Filament\Resources\RuiSezds\Tables\RuiSezdsTable;
use App\Models\RuiSezds;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RuiSezdsResource extends Resource
{
    protected static ?string $model = RuiSezds::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Sez. D';
    protected static string|UnitEnum|null $navigationGroup = 'OAM-RUI';

    public static function form(Schema $schema): Schema
    {
        return RuiSezdsForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuiSezdsTable::configure($table);
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
            'index' => ListRuiSezds::route('/'),
            'create' => CreateRuiSezds::route('/create'),
            'edit' => EditRuiSezds::route('/{record}/edit'),
        ];
    }
}
