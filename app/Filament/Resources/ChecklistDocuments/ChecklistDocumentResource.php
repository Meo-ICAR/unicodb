<?php

namespace App\Filament\Resources\ChecklistDocuments;

use App\Filament\Resources\ChecklistDocuments\Pages\CreateChecklistDocument;
use App\Filament\Resources\ChecklistDocuments\Pages\EditChecklistDocument;
use App\Filament\Resources\ChecklistDocuments\Pages\ListChecklistDocuments;
use App\Filament\Resources\ChecklistDocuments\Schemas\ChecklistDocumentForm;
use App\Filament\Resources\ChecklistDocuments\Tables\ChecklistDocumentsTable;
use App\Models\ChecklistDocument;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;

class ChecklistDocumentResource extends Resource
{
    protected static ?string $model = ChecklistDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isScopedToTenant = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ChecklistDocumentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ChecklistDocumentsTable::configure($table);
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
            'index' => ListChecklistDocuments::route('/'),
            'create' => CreateChecklistDocument::route('/create'),
            'edit' => EditChecklistDocument::route('/{record}/edit'),
        ];
    }
}
