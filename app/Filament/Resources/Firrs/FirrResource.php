<?php

namespace App\Filament\Resources\Firrs;

use App\Filament\Resources\Firrs\Pages\CreateFirr;
use App\Filament\Resources\Firrs\Pages\EditFirr;
use App\Filament\Resources\Firrs\Pages\ListFirrs;
use App\Filament\Resources\Firrs\Schemas\FirrForm;
use App\Filament\Resources\Firrs\Tables\FirrsTable;
use App\Models\Firr;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class FirrResource extends Resource
{
    protected static ?string $model = Firr::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $isScopedToTenant = false;

    protected static ?string $modelLabel = 'FIRR';

    protected static ?string $navigationLabel = 'FIRR';

    protected static ?string $pluralModelLabel = 'FIRR';

    protected static string|UnitEnum|null $navigationGroup = 'Elenchi';

    public static function form(Schema $schema): Schema
    {
        return FirrForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FirrsTable::configure($table);
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
            'index' => ListFirrs::route('/'),
        ];
    }
}
