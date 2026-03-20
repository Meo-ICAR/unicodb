<?php

namespace App\Filament\Resources\ApiConfigurations;

use App\Filament\Resources\ApiConfigurations\Pages\CreateApiConfiguration;
use App\Filament\Resources\ApiConfigurations\Pages\EditApiConfiguration;
use App\Filament\Resources\ApiConfigurations\Pages\ListApiConfigurations;
use App\Filament\Resources\ApiConfigurations\RelationManagers\ApiLogsRelationManager;
use App\Filament\Resources\ApiConfigurations\Schemas\ApiConfigurationForm;
use App\Filament\Resources\ApiConfigurations\Tables\ApiConfigurationsTable;
use App\Models\ApiConfiguration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ApiConfigurationResource extends Resource
{
    protected static ?string $model = ApiConfiguration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return ApiConfigurationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ApiConfigurationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ApiLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiConfigurations::route('/'),
            'create' => CreateApiConfiguration::route('/create'),
            'edit' => EditApiConfiguration::route('/{record}/edit'),
        ];
    }
}
