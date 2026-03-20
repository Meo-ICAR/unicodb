<?php

namespace App\Filament\Resources\RuiMandatis;

use App\Filament\Resources\RuiMandatis\Pages\CreateRuiMandati;
use App\Filament\Resources\RuiMandatis\Pages\EditRuiMandati;
use App\Filament\Resources\RuiMandatis\Pages\ListRuiMandatis;
use App\Filament\Resources\RuiMandatis\Schemas\RuiMandatiForm;
use App\Filament\Resources\RuiMandatis\Tables\RuiMandatisTable;
use App\Models\RuiMandati;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RuiMandatiResource extends Resource
{
    protected static ?string $model = RuiMandati::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Mandati';
    protected static string|UnitEnum|null $navigationGroup = 'OAM-RUI';

    public static function form(Schema $schema): Schema
    {
        return RuiMandatiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuiMandatisTable::configure($table);
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
            'index' => ListRuiMandatis::route('/'),
            'create' => CreateRuiMandati::route('/create'),
            'edit' => EditRuiMandati::route('/{record}/edit'),
        ];
    }
}
