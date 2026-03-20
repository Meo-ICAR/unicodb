<?php

namespace App\Filament\Resources\ChecklistAudits;

use App\Filament\Resources\ChecklistAudits\Pages\CreateChecklistAudit;
use App\Filament\Resources\ChecklistAudits\Pages\EditChecklistAudit;
use App\Filament\Resources\ChecklistAudits\Pages\ListChecklistAudits;
use App\Filament\Resources\ChecklistAudits\Schemas\ChecklistAuditForm;
use App\Filament\Resources\ChecklistAudits\Tables\ChecklistAuditsTable;
use App\Models\Checklist;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ChecklistAuditResource extends Resource
{
    protected static ?string $model = Checklist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Checklist Audit';

    protected static ?string $pluralModelLabel = 'Checklist Audit';

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Registro Audit';

    public static function form(Schema $schema): Schema
    {
        return ChecklistAuditForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChecklistAuditsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\ChecklistAudits\RelationManagers\ChecklistItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChecklistAudits::route('/'),
            'create' => CreateChecklistAudit::route('/create'),
            'edit' => EditChecklistAudit::route('/{record}/edit'),
        ];
    }

    /**
     * Filtra per mostrare solo le checklist con is_audit = true
     */
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('is_audit', true);
    }

    /**
     * Assicura che is_audit sia sempre true per le checklist create da questa resource
     */
    public static function beforeSave(array $data): array
    {
        $data['is_audit'] = true;
        return $data;
    }
}
