<?php

namespace App\Filament\Resources\AuditItems;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\AuditItems\Pages\CreateAuditItem;
use App\Filament\Resources\AuditItems\Pages\EditAuditItem;
use App\Filament\Resources\AuditItems\Pages\ListAuditItems;
use App\Filament\Resources\AuditItems\Schemas\AuditItemForm;
use App\Filament\Resources\AuditItems\Tables\AuditItemsTable;
use App\Models\AuditItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class AuditItemResource extends Resource
{
    protected static ?string $model = AuditItem::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationLabel = 'Dettaglio';
    protected static ?string $modelLabel = 'Dettaglio';
    protected static ?string $pluralModelLabel = 'Dettagli';
    protected static bool $isScopedToTenant = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    public static function form(Schema $schema): Schema
    {
        return AuditItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditItemsTable::configure($table);
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
            'index' => ListAuditItems::route('/'),
            'create' => CreateAuditItem::route('/create'),
            'edit' => EditAuditItem::route('/{record}/edit'),
        ];
    }
}
