<?php

namespace App\Filament\Resources\ProformaStatuses;

use App\Filament\Resources\ProformaStatuses\Pages\CreateProformaStatus;
use App\Filament\Resources\ProformaStatuses\Pages\EditProformaStatus;
use App\Filament\Resources\ProformaStatuses\Pages\ListProformaStatuses;
use App\Filament\Resources\ProformaStatuses\Schemas\ProformaStatusForm;
use App\Filament\Resources\ProformaStatuses\Tables\ProformaStatusesTable;
use App\Models\ProformaStatus;
use App\Models\ProformaStatu;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProformaStatusResource extends Resource
{
    protected static ?string $model = ProformaStatus::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';


    public static function form(Schema $schema): Schema
    {
        return ProformaStatusForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProformaStatusesTable::configure($table);
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
            'index' => ListProformaStatuses::route('/'),
            'create' => CreateProformaStatus::route('/create'),
            'edit' => EditProformaStatus::route('/{record}/edit'),
        ];
    }
}
