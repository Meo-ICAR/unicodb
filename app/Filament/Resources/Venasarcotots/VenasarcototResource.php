<?php

namespace App\Filament\Resources\Venasarcotots;

use App\Filament\Resources\Venasarcotots\Pages\CreateVenasarcotot;
use App\Filament\Resources\Venasarcotots\Pages\EditVenasarcotot;
use App\Filament\Resources\Venasarcotots\Pages\ListVenasarcotots;
use App\Filament\Resources\Venasarcotots\Schemas\VenasarcototForm;
use App\Filament\Resources\Venasarcotots\Tables\VenasarcototsTable;
use App\Models\VenasarcoTot;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class VenasarcototResource extends Resource
{
    protected static ?string $model = Venasarcotot::class;
    protected static string|UnitEnum|null $navigationGroup = 'Amministrazione';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Enasarco';
    protected static ?string $modelLabel = 'Enasarco';
    protected static ?string $pluralModelLabel = 'Enasarco';

    public static function form(Schema $schema): Schema
    {
        return VenasarcototForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VenasarcototsTable::configure($table);
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
            'index' => ListVenasarcotots::route('/'),
            'create' => CreateVenasarcotot::route('/create'),
            'edit' => EditVenasarcotot::route('/{record}/edit'),
        ];
    }
}
