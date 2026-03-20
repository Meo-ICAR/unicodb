<?php

namespace App\Filament\Resources\RuiAccessoris;

use App\Filament\Resources\RuiAccessoris\Pages\CreateRuiAccessoris;
use App\Filament\Resources\RuiAccessoris\Pages\EditRuiAccessoris;
use App\Filament\Resources\RuiAccessoris\Pages\ListRuiAccessoris;
use App\Filament\Resources\RuiAccessoris\Schemas\RuiAccessorisForm;
use App\Filament\Resources\RuiAccessoris\Tables\RuiAccessorisTable;
use App\Models\RuiAccessoris;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RuiAccessorisResource extends Resource
{
    protected static ?string $model = RuiAccessoris::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $navigationLabel = 'Accessori';
    protected static ?string $modelLabel = 'Accessorio';
    protected static ?string $pluralModelLabel = 'Accessori';
    protected static string|UnitEnum|null $navigationGroup = 'OAM-RUI';

    public static function form(Schema $schema): Schema
    {
        return RuiAccessorisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuiAccessorisTable::configure($table);
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
            'index' => ListRuiAccessoris::route('/'),
            'create' => CreateRuiAccessoris::route('/create'),
            'edit' => EditRuiAccessoris::route('/{record}/edit'),
        ];
    }
}
