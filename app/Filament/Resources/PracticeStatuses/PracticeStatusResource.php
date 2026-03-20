<?php

namespace App\Filament\Resources\PracticeStatuses;

use App\Filament\Resources\PracticeStatuses\Pages\CreatePracticeStatus;
use App\Filament\Resources\PracticeStatuses\Pages\EditPracticeStatus;
use App\Filament\Resources\PracticeStatuses\Pages\ListPracticeStatuses;
use App\Filament\Resources\PracticeStatuses\Schemas\PracticeStatusForm;
use App\Filament\Resources\PracticeStatuses\Tables\PracticeStatusesTable;
use App\Models\PracticeStatus;
use App\Models\PracticeStatu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PracticeStatusResource extends Resource
{
    protected static ?string $model = PracticeStatus::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';


    public static function form(Schema $schema): Schema
    {
        return PracticeStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticeStatusesTable::configure($table);
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
            'index' => ListPracticeStatuses::route('/'),
            'create' => CreatePracticeStatus::route('/create'),
            'edit' => EditPracticeStatus::route('/{record}/edit'),
        ];
    }
}
