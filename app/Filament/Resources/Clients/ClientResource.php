<?php

namespace App\Filament\Resources\Clients;

use App\Filament\RelationManagers\AddressesRelationManager;
use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\RelationManagers\PurchaseInvoicesRelationManager;
use App\Filament\RelationManagers\SalesInvoicesRelationManager;
use App\Filament\RelationManagers\WebsitesRelationManager;
use App\Filament\Resources\Clients\Pages\CreateClient;
use App\Filament\Resources\Clients\Pages\EditClient;
use App\Filament\Resources\Clients\Pages\ListClients;
use App\Filament\Resources\Clients\RelationManagers\ChecklistsRelationManager;
use App\Filament\Resources\Clients\RelationManagers\ClientMandatesRelationManager;
use App\Filament\Resources\Clients\RelationManagers\ClientRelationsRelationManager;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Tables\ClientsTable;
use App\Filament\Traits\HasChecklistAction;
use App\Models\Client;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use UnitEnum;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    // 2. Usa il Trait nella classe della Risorsa
    use HasChecklistAction;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Pratiche';

    protected static ?string $navigationLabel = 'Anagrafiche';

    protected static ?string $modelLabel = 'Anagrafica';

    protected static ?string $pluralModelLabel = 'Anagrafiche';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            AddressesRelationManager::class,
            DocumentsRelationManager::class,
            WebsitesRelationManager::class,
            ClientRelationsRelationManager::class,
            ClientMandatesRelationManager::class,
            ChecklistsRelationManager::class,
            SalesInvoicesRelationManager::class,
            PurchaseInvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClients::route('/'),
            'create' => CreateClient::route('/create'),
            'edit' => EditClient::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_company', false);
    }
}
