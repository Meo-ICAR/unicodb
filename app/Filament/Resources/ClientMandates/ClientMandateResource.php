<?php

namespace App\Filament\Resources\ClientMandates;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\ClientMandates\Pages\CreateClientMandate;
use App\Filament\Resources\ClientMandates\Pages\EditClientMandate;
use App\Filament\Resources\ClientMandates\Pages\ListClientMandates;
use App\Filament\Resources\ClientMandates\RelationManagers\ChecklistsRelationManager;
use App\Filament\Resources\ClientMandates\Schemas\ClientMandateForm;
use App\Filament\Resources\ClientMandates\Tables\ClientMandatesTable;
use App\Filament\Traits\HasChecklistAction;
use App\Models\ClientMandate;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BackedEnum;
use UnitEnum;

class ClientMandateResource extends Resource
{
    protected static ?string $model = ClientMandate::class;

    // 2. Usa il Trait nella classe della Risorsa

    use HasChecklistAction;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return ClientMandateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientMandatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PracticesRelationManager::class,
            DocumentsRelationManager::class,
            ChecklistsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientMandates::route('/'),
            'create' => CreateClientMandate::route('/create'),
            'edit' => EditClientMandate::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
