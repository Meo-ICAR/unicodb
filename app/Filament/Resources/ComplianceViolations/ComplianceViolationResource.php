<?php

namespace App\Filament\Resources\ComplianceViolations;

use App\Filament\Resources\ComplianceViolations\Pages\CreateComplianceViolation;
use App\Filament\Resources\ComplianceViolations\Pages\EditComplianceViolation;
use App\Filament\Resources\ComplianceViolations\Pages\ListComplianceViolations;
use App\Filament\Resources\ComplianceViolations\Schemas\ComplianceViolationForm;
use App\Filament\Resources\ComplianceViolations\Tables\ComplianceViolationsTable;
use App\Models\ComplianceViolation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class ComplianceViolationResource extends Resource
{
    protected static ?string $model = ComplianceViolation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Registro Violazioni';

    protected static ?string $modelLabel = 'Violazione';

    protected static ?string $pluralModelLabel = 'Violazioni';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return ComplianceViolationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ComplianceViolationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListComplianceViolations::route('/'),
            'create' => CreateComplianceViolation::route('/create'),
            'edit' => EditComplianceViolation::route('/{record}/edit'),
        ];
    }
}
