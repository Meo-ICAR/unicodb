<?php

namespace App\Filament\Resources\Abis;

use App\Filament\Resources\Abis\Pages\CreateAbi;
use App\Filament\Resources\Abis\Pages\EditAbi;
use App\Filament\Resources\Abis\Pages\ListAbis;
use App\Filament\Resources\Abis\Schemas\AbiForm;
use App\Filament\Resources\Abis\Tables\AbisTable;
use App\Models\Abi;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class AbiResource extends Resource
{
    protected static ?string $model = Abi::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Banche';

    protected static ?string $modelLabel = 'Banca';

    protected static ?string $pluralModelLabel = 'Banche';

    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';

    public static function form(Schema $schema): Schema
    {
        return AbiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AbisTable::configure($table);
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
            'index' => ListAbis::route('/'),
        ];
    }
}
