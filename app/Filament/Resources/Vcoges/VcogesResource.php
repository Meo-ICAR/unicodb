<?php

namespace App\Filament\Resources\Vcoges;

use App\Filament\Resources\Vcoges\Pages\CreateVcoges;
use App\Filament\Resources\Vcoges\Pages\EditVcoges;
use App\Filament\Resources\Vcoges\Pages\ListVcoges;
use App\Filament\Resources\Vcoges\Schemas\VcogesForm;
use App\Filament\Resources\Vcoges\Tables\VcogesTable;
use App\Models\Vcoge;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class VcogesResource extends Resource
{
    protected static ?string $model = Vcoge::class;

    protected static string|UnitEnum|null $navigationGroup = 'Amministrazione';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Primanota';

    protected static ?string $modelLabel = 'Primanota';

    protected static ?string $pluralModelLabel = 'Primenote';

    public static function form(Schema $schema): Schema
    {
        return VcogesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VcogesTable::configure($table);
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
            'index' => ListVcoges::route('/'),
            'create' => CreateVcoges::route('/create'),
            'edit' => EditVcoges::route('/{record}/edit'),
        ];
    }
}
