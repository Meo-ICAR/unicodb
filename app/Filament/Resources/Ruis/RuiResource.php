<?php

namespace App\Filament\Resources\Ruis;

use App\Filament\Resources\Ruis\Pages\CreateRui;
use App\Filament\Resources\Ruis\Pages\EditRui;
use App\Filament\Resources\Ruis\Pages\ListRuis;
use App\Filament\Resources\Ruis\Schemas\RuiForm;
use App\Filament\Resources\Ruis\Tables\RuisTable;
use App\Models\Rui;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RuiResource extends Resource
{
    protected static ?string $model = Rui::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'RUI Intermediari';
    protected static string|UnitEnum|null $navigationGroup = 'OAM-RUI';

    public static function form(Schema $schema): Schema
    {
        return RuiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuisTable::configure($table);
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
            'index' => ListRuis::route('/'),
            'create' => CreateRui::route('/create'),
            'edit' => EditRui::route('/{record}/edit'),
        ];
    }
}
