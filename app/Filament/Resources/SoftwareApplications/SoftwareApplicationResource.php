<?php

namespace App\Filament\Resources\SoftwareApplications;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\SoftwareApplications\Pages\CreateSoftwareApplication;
use App\Filament\Resources\SoftwareApplications\Pages\EditSoftwareApplication;
use App\Filament\Resources\SoftwareApplications\Pages\ListSoftwareApplications;
use App\Filament\Resources\SoftwareApplications\RelationManagers\ApiConfigurationsRelationManager;
use App\Filament\Resources\SoftwareApplications\RelationManagers\SoftwareCategoriesRelationManager;
use App\Filament\Resources\SoftwareApplications\RelationManagers\SoftwareMappingsRelationManager;
use App\Filament\Resources\SoftwareApplications\Schemas\SoftwareApplicationForm;
use App\Filament\Resources\SoftwareApplications\Tables\SoftwareApplicationsTable;
use App\Models\SoftwareApplication;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class SoftwareApplicationResource extends Resource
{
    protected static ?string $model = SoftwareApplication::class;

    protected static ?string $navigationLabel = 'Software';

    protected static ?string $modelLabel = 'Software';

    protected static ?string $pluralModelLabel = 'Software';

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';

    public static function form(Schema $schema): Schema
    {
        return SoftwareApplicationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SoftwareApplicationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ApiConfigurationsRelationManager::class,
            SoftwareMappingsRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSoftwareApplications::route('/'),
            'create' => CreateSoftwareApplication::route('/create'),
            'edit' => EditSoftwareApplication::route('/{record}/edit'),
        ];
    }
}
