<?php

namespace App\Filament\Resources\RegulatoryBodies;

use App\Filament\Resources\RegulatoryBodies\Pages\CreateRegulatoryBody;
use App\Filament\Resources\RegulatoryBodies\Pages\EditRegulatoryBody;
use App\Filament\Resources\RegulatoryBodies\Pages\ListRegulatoryBodies;
use App\Filament\Resources\RegulatoryBodies\Schemas\RegulatoryBodyForm;
use App\Filament\Resources\RegulatoryBodies\Tables\RegulatoryBodiesTable;
use App\Models\RegulatoryBody;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use AppFilamentResourcesRegulatoryBodiesPagesCreateRegulatoryBody;
use AppFilamentResourcesRegulatoryBodiesPagesEditRegulatoryBody;
use AppFilamentResourcesRegulatoryBodiesPagesListRegulatoryBody;
use BackedEnum;
use UnitEnum;

class RegulatoryBodyResource extends Resource
{
    protected static ?string $model = RegulatoryBody::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;

    protected static ?string $modelLabel = 'Ente';

    protected static ?string $navigationLabel = 'Enti';

    protected static ?string $pluralModelLabel = 'Enti';

    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';

    public static function form(Schema $schema): Schema
    {
        return RegulatoryBodyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RegulatoryBodiesTable::configure($table);
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
            'index' => ListRegulatoryBodies::route('/'),
        ];
    }
}
