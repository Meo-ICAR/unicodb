<?php

namespace App\Filament\Resources\ProformaStatusHistories;
use AppFilamentResourcesProformaStatusHistoriesPagesCreateProformaStatusHistory;
use AppFilamentResourcesProformaStatusHistoriesPagesEditProformaStatusHistory;
use AppFilamentResourcesProformaStatusHistoriesPagesListProformaStatusHistory;

use App\Filament\Resources\ProformaStatusHistories\Pages\CreateProformaStatusHistory;
use App\Filament\Resources\ProformaStatusHistories\Pages\EditProformaStatusHistory;
use App\Filament\Resources\ProformaStatusHistories\Pages\ListProformaStatusHistories;
use App\Filament\Resources\ProformaStatusHistories\Schemas\ProformaStatusHistoryForm;
use App\Filament\Resources\ProformaStatusHistories\Tables\ProformaStatusHistoriesTable;
use App\Models\ProformaStatusHistory;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ProformaStatusHistoryResource extends Resource
{
    protected static ?string $model = ProformaStatusHistory::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    public static function form(Schema $schema): Schema
    {
        return ProformaStatusHistoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProformaStatusHistoriesTable::configure($table);
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
            'index' => ListProformaStatusHistories::route('/'),
            'create' => CreateProformaStatusHistory::route('/create'),
            'edit' => EditProformaStatusHistory::route('/{record}/edit'),
        ];
    }
}
