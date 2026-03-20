<?php

namespace App\Filament\Resources\Agents;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\RelationManagers\PurchaseInvoicesRelationManager;
use App\Filament\RelationManagers\WebsitesRelationManager;
use App\Filament\Resources\Agents\Pages\CreateAgent;
use App\Filament\Resources\Agents\Pages\EditAgent;
use App\Filament\Resources\Agents\Pages\ListAgents;
use App\Filament\Resources\Agents\RelationManagers\ChecklistsRelationManager;
use App\Filament\Resources\Agents\RelationManagers\TrainingRecordsRelationManager;
use App\Filament\Resources\Agents\Schemas\AgentForm;
use App\Filament\Resources\Agents\Tables\AgentsTable;
use App\Models\Agent;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'Organizzazione';

    protected static ?string $navigationLabel = 'Agenti';

    protected static ?string $modelLabel = 'Agente';

    protected static ?string $pluralModelLabel = 'Agenti';

    public static function form(Schema $schema): Schema
    {
        return AgentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AgentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            WebsitesRelationManager::class,
            ChecklistsRelationManager::class,
            TrainingRecordsRelationManager::class,
            PurchaseInvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgents::route('/'),
            'create' => CreateAgent::route('/create'),
            'edit' => EditAgent::route('/{record}/edit'),
        ];
    }
}
