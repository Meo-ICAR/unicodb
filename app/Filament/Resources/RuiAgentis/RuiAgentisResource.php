<?php

namespace App\Filament\Resources\RuiAgentis;

use App\Filament\Resources\RuiAgentis\Pages\CreateRuiAgentis;
use App\Filament\Resources\RuiAgentis\Pages\EditRuiAgentis;
use App\Filament\Resources\RuiAgentis\Pages\ListRuiAgentis;
use App\Filament\Resources\RuiAgentis\Schemas\RuiAgentisForm;
use App\Filament\Resources\RuiAgentis\Tables\RuiAgentisTable;
use App\Models\RuiAgentis;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RuiAgentisResource extends Resource
{
    protected static ?string $model = RuiAgentis::class;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static ?string $navigationLabel = 'Agenti D';
    protected static string|UnitEnum|null $navigationGroup = 'OAM-RUI';
    protected static ?string $modelLabel = 'Agenti D';
    protected static ?string $pluralModelLabel = 'Agenti';

    public static function form(Schema $schema): Schema
    {
        return RuiAgentisForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RuiAgentisTable::configure($table);
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
            'index' => ListRuiAgentis::route('/'),
            'create' => CreateRuiAgentis::route('/create'),
            'edit' => EditRuiAgentis::route('/{record}/edit'),
        ];
    }
}
