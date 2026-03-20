<?php

namespace App\Filament\Resources\ClientRelations;

use App\Filament\Resources\ClientRelations\Pages\CreateClientRelation;
use App\Filament\Resources\ClientRelations\Pages\EditClientRelation;
use App\Filament\Resources\ClientRelations\Pages\ListClientRelations;
use App\Filament\Resources\ClientRelations\Schemas\ClientRelationForm;
use App\Filament\Resources\ClientRelations\Tables\ClientRelationsTable;
use App\Models\ClientRelation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;

class ClientRelationResource extends Resource
{
    protected static ?string $model = ClientRelation::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ClientRelationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientRelationsTable::configure($table);
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
            'index' => ListClientRelations::route('/'),
            'create' => CreateClientRelation::route('/create'),
            'edit' => EditClientRelation::route('/{record}/edit'),
        ];
    }
}
