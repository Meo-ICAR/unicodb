<?php

namespace App\Filament\Resources\ApiLogs;

use App\Filament\Resources\ApiLogs\Pages\CreateApiLog;
use App\Filament\Resources\ApiLogs\Pages\EditApiLog;
use App\Filament\Resources\ApiLogs\Pages\ListApiLogs;
use App\Filament\Resources\ApiLogs\Schemas\ApiLogForm;
use App\Filament\Resources\ApiLogs\Tables\ApiLogsTable;
use App\Models\ApiLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ApiLogResource extends Resource
{
    protected static ?string $model = ApiLog::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;
    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    public static function form(Schema $schema): Schema
    {
        return ApiLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiLogsTable::configure($table);
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
            'index' => ListApiLogs::route('/'),
            'create' => CreateApiLog::route('/create'),
            'edit' => EditApiLog::route('/{record}/edit'),
        ];
    }
}
