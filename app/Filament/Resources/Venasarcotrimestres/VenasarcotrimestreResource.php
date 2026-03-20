<?php

namespace App\Filament\Resources\Venasarcotrimestres;

use App\Filament\Resources\Venasarcotrimestres\Pages\CreateVenasarcotrimestre;
use App\Filament\Resources\Venasarcotrimestres\Pages\EditVenasarcotrimestre;
use App\Filament\Resources\Venasarcotrimestres\Pages\ListVenasarcotrimestres;
use App\Filament\Resources\Venasarcotrimestres\Schemas\VenasarcotrimestreForm;
use App\Filament\Resources\Venasarcotrimestres\Tables\VenasarcotrimestresTable;
use App\Models\VenasarcoTrimestre;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class VenasarcotrimestreResource extends Resource
{
    protected static ?string $model = Venasarcotrimestre::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static string|UnitEnum|null $navigationGroup = 'Amministrazione';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return VenasarcotrimestreForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VenasarcotrimestresTable::configure($table);
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
            'index' => ListVenasarcotrimestres::route('/'),
            'create' => CreateVenasarcotrimestre::route('/create'),
            'edit' => EditVenasarcotrimestre::route('/{record}/edit'),
        ];
    }
}
