<?php

namespace App\Filament\Resources\DocumentTypes;

use App\Filament\Resources\DocumentTypes\Pages\CreateDocumentType;
use App\Filament\Resources\DocumentTypes\Pages\EditDocumentType;
use App\Filament\Resources\DocumentTypes\Pages\ListDocumentTypes;
use App\Filament\Resources\DocumentTypes\Schemas\DocumentTypeForm;
use App\Filament\Resources\DocumentTypes\Tables\DocumentTypesTable;
use App\Models\DocumentType;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class DocumentTypeResource extends Resource
{
    protected static ?string $model = DocumentType::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    protected static ?string $navigationLabel = 'Tipi Documento';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Tipo Documento';

    protected static ?string $pluralModelLabel = 'Tipi Documento';

    public static function form(Schema $schema): Schema
    {
        return DocumentTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentTypesTable::configure($table);
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
            'index' => ListDocumentTypes::route('/'),
            'create' => CreateDocumentType::route('/create'),
            'edit' => EditDocumentType::route('/{record}/edit'),
        ];
    }
}
