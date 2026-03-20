<?php

namespace App\Filament\Resources\SoftwareApplications\RelationManagers;

use App\Filament\Resources\SoftwareMappings\Schemas\SoftwareMappingForm;
use App\Filament\Resources\SoftwareMappings\Tables\SoftwareMappingsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class SoftwareMappingsRelationManager extends RelationManager
{
    protected static string $relationship = 'softwareMappings';

    protected static ?string $title = 'Mapping Software';

    public function form(Schema $schema): Schema
    {
        return SoftwareMappingForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return SoftwareMappingsTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
