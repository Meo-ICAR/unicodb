<?php

namespace App\Filament\Resources\DocumentScopes;

use App\Filament\Resources\DocumentScopes\Pages\CreateDocumentScope;
use App\Filament\Resources\DocumentScopes\Pages\EditDocumentScope;
use App\Filament\Resources\DocumentScopes\Pages\ListDocumentScopes;
use App\Filament\Resources\DocumentScopes\Schemas\DocumentScopeForm;
use App\Filament\Resources\DocumentScopes\Tables\DocumentScopesTable;
use App\Models\DocumentScope;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class DocumentScopeResource extends Resource
{
    protected static ?string $model = DocumentScope::class;

    protected static bool $isScopedToTenant = false;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEye;

    public static function form(Schema $schema): Schema
    {
        return DocumentScopeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DocumentScopesTable::configure($table);
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
            'index' => ListDocumentScopes::route('/'),
            'create' => CreateDocumentScope::route('/create'),
            'edit' => EditDocumentScope::route('/{record}/edit'),
        ];
    }
}
