<?php

namespace App\Filament\Resources\RaciAssignements;

use App\Filament\Resources\RaciAssignements\Pages\CreateRaciAssignement;
use App\Filament\Resources\RaciAssignements\Pages\EditRaciAssignement;
use App\Filament\Resources\RaciAssignements\Pages\ListRaciAssignements;
use App\Filament\Resources\RaciAssignements\Schemas\RaciAssignementForm;
use App\Filament\Resources\RaciAssignements\Tables\RaciAssignementsTable;
use App\Models\RaciAssignment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RaciAssignementResource extends Resource
{
    protected static ?string $model = RaciAssignment::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'RACI';

    protected static ?string $modelLabel = 'RACI - Assegnazione';

    protected static ?string $pluralModelLabel = 'RACI';

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    public static function form(Schema $schema): Schema
    {
        return RaciAssignementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RaciAssignementsTable::configure($table);
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
            'index' => ListRaciAssignements::route('/'),
            'create' => CreateRaciAssignement::route('/create'),
            'edit' => EditRaciAssignement::route('/{record}/edit'),
        ];
    }
}
