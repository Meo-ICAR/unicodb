<?php

namespace App\Filament\Resources\EnasarcoLimits;

use App\Filament\Resources\EnasarcoLimits\Pages\CreateEnasarcoLimit;
use App\Filament\Resources\EnasarcoLimits\Pages\EditEnasarcoLimit;
use App\Filament\Resources\EnasarcoLimits\Pages\ListEnasarcoLimits;
use App\Filament\Resources\EnasarcoLimits\Schemas\EnasarcoLimitForm;
use App\Filament\Resources\EnasarcoLimits\Tables\EnasarcoLimitsTable;
use App\Models\EnasarcoLimit;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class EnasarcoLimitResource extends Resource
{
    protected static ?string $model = EnasarcoLimit::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyEuro;

    protected static ?string $modelLabel = 'Massimali';

    protected static ?string $navigationLabel = 'Enasarco Massimali';

    // protected static ?string $pluralModelLabel = 'Enasarco';

    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';

    public static function form(Schema $schema): Schema
    {
        return EnasarcoLimitForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EnasarcoLimitsTable::configure($table);
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
            'index' => ListEnasarcoLimits::route('/'),
        ];
    }
}
