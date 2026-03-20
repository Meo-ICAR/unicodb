<?php

namespace App\Filament\Resources\RuiWebSites;

use App\Filament\Resources\RuiWebSites\Pages\CreateRuiWebSites;
use App\Filament\Resources\RuiWebSites\Pages\EditRuiWebSites;
use App\Filament\Resources\RuiWebSites\Pages\ListRuiWebSites;
use App\Filament\Resources\RuiWebSites\Schemas\RuiWebSitesForm;
use App\Filament\Resources\RuiWebSites\Tables\RuiWebSitesTable;
use App\Models\RuiWebsite;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RuiWebSitesResource extends Resource
{
    protected static ?string $model = RuiWebsite::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $navigationLabel = 'Web Sites';
    protected static string|UnitEnum|null $navigationGroup = 'OAM-RUI';

    public static function form(Schema $schema): Schema
    {
        return RuiWebSitesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuiWebSitesTable::configure($table);
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
            'index' => ListRuiWebSites::route('/'),
            'create' => CreateRuiWebSites::route('/create'),
            'edit' => EditRuiWebSites::route('/{record}/edit'),
        ];
    }
}
