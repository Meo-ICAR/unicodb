<?php

namespace App\Filament\Resources\SoftwareMappings;

use App\Filament\Resources\SoftwareMappings\Pages\CreateSoftwareMapping;
use App\Filament\Resources\SoftwareMappings\Pages\EditSoftwareMapping;
use App\Filament\Resources\SoftwareMappings\Pages\ListSoftwareMappings;
use App\Filament\Resources\SoftwareMappings\Schemas\SoftwareMappingForm;
use App\Filament\Resources\SoftwareMappings\Tables\SoftwareMappingsTable;
use App\Models\SoftwareMapping;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class SoftwareMappingResource extends Resource
{
    protected static ?string $model = SoftwareMapping::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return SoftwareMappingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SoftwareMappingsTable::configure($table);
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
            'index' => ListSoftwareMappings::route('/'),
            'create' => CreateSoftwareMapping::route('/create'),
            'edit' => EditSoftwareMapping::route('/{record}/edit'),
        ];
    }
}
