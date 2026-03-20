<?php

namespace App\Filament\Resources\Comunes;

use App\Filament\Resources\Comunes\Pages\CreateComune;
use App\Filament\Resources\Comunes\Pages\EditComune;
use App\Filament\Resources\Comunes\Pages\ListComunes;
use App\Filament\Resources\Comunes\Schemas\ComuneForm;
use App\Filament\Resources\Comunes\Tables\ComunesTable;
use App\Models\Comune;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ComuneResource extends Resource
{
    protected static ?string $model = Comune::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $modelLabel = 'Comune';
    protected static ?string $navigationLabel = 'Comuni';
    protected static ?string $pluralModelLabel = 'Comuni';
    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';

    public static function form(Schema $schema): Schema
    {
        return ComuneForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComunesTable::configure($table);
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
            'index' => ListComunes::route('/'),
        ];
    }
}
