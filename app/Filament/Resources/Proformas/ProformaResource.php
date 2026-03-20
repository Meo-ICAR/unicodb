<?php

namespace App\Filament\Resources\Proformas;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Proformas\Pages\CreateProforma;
use App\Filament\Resources\Proformas\Pages\EditProforma;
use App\Filament\Resources\Proformas\Pages\ListProformas;
use App\Filament\Resources\Proformas\Schemas\ProformaForm;
use App\Filament\Resources\Proformas\Tables\ProformasTable;
use App\Models\Proforma;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ProformaResource extends Resource
{
    protected static ?string $model = Proforma::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Proforma';

    protected static ?string $modelLabel = 'Proforma';

    protected static ?string $pluralModelLabel = 'Proforma';

    protected static string|UnitEnum|null $navigationGroup = 'Amministrazione';

    public static function form(Schema $schema): Schema
    {
        return ProformaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProformasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProformas::route('/'),
            'create' => CreateProforma::route('/create'),
            'edit' => EditProforma::route('/{record}/edit'),
        ];
    }
}
