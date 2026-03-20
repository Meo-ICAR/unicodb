<?php

namespace App\Filament\Resources\Audits;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Audits\Pages\CreateAudit;
use App\Filament\Resources\Audits\Pages\EditAudit;
use App\Filament\Resources\Audits\Pages\ListAudits;
use App\Filament\Resources\Audits\RelationManagers\AuditItemsRelationManager;
use App\Filament\Resources\Audits\Schemas\AuditForm;
use App\Filament\Resources\Audits\Tables\AuditsTable;
use App\Models\Audit;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Registro Audit';

    protected static ?string $modelLabel = 'Audit';

    protected static ?string $pluralModelLabel = 'Audit';

    public static function form(Schema $schema): Schema
    {
        return AuditForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuditsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            AuditItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAudits::route('/'),
            'create' => CreateAudit::route('/create'),
            'edit' => EditAudit::route('/{record}/edit'),
        ];
    }
}
