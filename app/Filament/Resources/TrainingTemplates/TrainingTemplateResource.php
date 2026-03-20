<?php

namespace App\Filament\Resources\TrainingTemplates;

use App\Filament\Resources\TrainingTemplates\Pages\CreateTrainingTemplate;
use App\Filament\Resources\TrainingTemplates\Pages\EditTrainingTemplate;
use App\Filament\Resources\TrainingTemplates\Pages\ListTrainingTemplates;
use App\Filament\Resources\TrainingTemplates\Schemas\TrainingTemplateForm;
use App\Filament\Resources\TrainingTemplates\Tables\TrainingTemplatesTable;
use App\Models\TrainingTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class TrainingTemplateResource extends Resource
{
    protected static ?string $model = TrainingTemplate::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Tabelle';


    public static function form(Schema $schema): Schema
    {
        return TrainingTemplateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingTemplatesTable::configure($table);
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
            'index' => ListTrainingTemplates::route('/'),
            'create' => CreateTrainingTemplate::route('/create'),
            'edit' => EditTrainingTemplate::route('/{record}/edit'),
        ];
    }
}
