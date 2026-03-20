<?php

namespace App\Filament\Resources\Remediations;

use App\Filament\Resources\Remediations\Pages\CreateRemediation;
use App\Filament\Resources\Remediations\Pages\EditRemediation;
use App\Filament\Resources\Remediations\Pages\ListRemediations;
use App\Filament\Resources\Remediations\Schemas\RemediationForm;
use App\Filament\Resources\Remediations\Tables\RemediationsTable;
use App\Models\Remediation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class RemediationResource extends Resource
{
    protected static ?string $model = Remediation::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Azioni di rimedio';

    protected static ?string $modelLabel = 'Rimedio';

    protected static ?string $pluralModelLabel = 'Azioni di rimedio';

    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';

    public static function form(Schema $schema): Schema
    {
        return RemediationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RemediationsTable::configure($table);
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
            'index' => ListRemediations::route('/'),
            'create' => CreateRemediation::route('/create'),
            'edit' => EditRemediation::route('/{record}/edit'),
        ];
    }
}
