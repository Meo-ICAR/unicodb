<?php

namespace App\Filament\Resources\DocumentTypeScopes;

use App\Filament\Resources\DocumentTypeScopes\Pages\ListDocumentTypeScopes;
use App\Filament\Resources\DocumentTypeScopes\Pages\CreateDocumentTypeScope;
use App\Filament\Resources\DocumentTypeScopes\Pages\EditDocumentTypeScope;
use App\Filament\Resources\DocumentTypeScopes\Schemas\DocumentTypeScopeForm;
use App\Filament\Resources\DocumentTypeScopes\Tables\DocumentTypeScopesTable;
use App\Models\DocumentTypeScope;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class DocumentTypeScopeResource extends Resource
{
    protected static ?string $model = DocumentTypeScope::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    public static function form(Schema $schema): Schema
    {
        return DocumentTypeScopeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentTypeScopesTable::configure($table);
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
            'index' => ListDocumentTypeScopes::route('/'),
            'create' => CreateDocumentTypeScope::route('/create'),
            'edit' => EditDocumentTypeScope::route('/{record}/edit'),
        ];
    }
}
