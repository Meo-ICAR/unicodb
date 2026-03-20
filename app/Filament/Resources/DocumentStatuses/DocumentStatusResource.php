<?php

namespace App\Filament\Resources\DocumentStatuses;

use App\Filament\Resources\DocumentStatuses\Pages\CreateDocumentStatus;
use App\Filament\Resources\DocumentStatuses\Pages\EditDocumentStatus;
use App\Filament\Resources\DocumentStatuses\Pages\ListDocumentStatuses;
use App\Filament\Resources\DocumentStatuses\Schemas\DocumentStatusForm;
use App\Filament\Resources\DocumentStatuses\Tables\DocumentStatusesTable;
use App\Models\DocumentStatus;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;

class DocumentStatusResource extends Resource
{
    protected static ?string $model = DocumentStatus::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    protected static ?string $navigationLabel = 'Stati Documenti';

    protected static ?string $modelLabel = 'Stato Documento';

    protected static ?string $pluralModelLabel = 'Stati Documenti';

    protected static ?int $navigationSort = 8;

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return DocumentStatusForm::configure($schema);
    }

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return DocumentStatusesTable::configure($table);
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
            'index' => ListDocumentStatuses::route('/'),
            'create' => CreateDocumentStatus::route('/create'),
            'edit' => EditDocumentStatus::route('/{record}/edit'),
        ];
    }
}
