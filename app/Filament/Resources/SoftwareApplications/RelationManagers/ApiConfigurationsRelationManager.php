<?php

namespace App\Filament\Resources\SoftwareApplications\RelationManagers;

use App\Filament\Resources\ApiConfigurations\Schemas\ApiConfigurationForm;
use App\Filament\Resources\ApiConfigurations\Tables\ApiConfigurationsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class ApiConfigurationsRelationManager extends RelationManager
{
    protected static string $relationship = 'apiConfigurations';

    protected static ?string $title = 'Configurazioni API';

    public function form(Schema $schema): Schema
    {
        return ApiConfigurationForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ApiConfigurationsTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
