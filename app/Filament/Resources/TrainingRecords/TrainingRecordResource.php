<?php

namespace App\Filament\Resources\TrainingRecords;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\TrainingRecords\Pages\CreateTrainingRecord;
use App\Filament\Resources\TrainingRecords\Pages\EditTrainingRecord;
use App\Filament\Resources\TrainingRecords\Pages\ListTrainingRecords;
use App\Filament\Resources\TrainingRecords\Schemas\TrainingRecordForm;
use App\Filament\Resources\TrainingRecords\Tables\TrainingRecordsTable;
use App\Models\TrainingRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class TrainingRecordResource extends Resource
{
    protected static ?string $model = TrainingRecord::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $navigationLabel = 'Registro Formazione';

    protected static ?string $modelLabel = 'Formazione';

    protected static ?string $pluralModelLabel = 'Formazione';

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return TrainingRecordForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingRecordsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingRecords::route('/'),
            'create' => CreateTrainingRecord::route('/create'),
            'edit' => EditTrainingRecord::route('/{record}/edit'),
        ];
    }
}
