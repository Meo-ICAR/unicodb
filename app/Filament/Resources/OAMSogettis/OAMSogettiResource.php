<?php

namespace App\Filament\Resources\OAMSogettis;

use App\Filament\Resources\OAMSogettis\Pages\CreateOAMSogetti;
use App\Filament\Resources\OAMSogettis\Pages\EditOAMSogetti;
use App\Filament\Resources\OAMSogettis\Pages\ListOAMSogettis;
use App\Filament\Resources\OAMSogettis\Schemas\OAMSogettiForm;
use App\Filament\Resources\OAMSogettis\Tables\OAMSogettisTable;
use App\Models\OAMSogetti;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OAMSogettiResource extends Resource
{
    protected static ?string $model = OAMSogetti::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OAMSogettiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OAMSogettisTable::configure($table);
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
            'index' => ListOAMSogettis::route('/'),
            'create' => CreateOAMSogetti::route('/create'),
            'edit' => EditOAMSogetti::route('/{record}/edit'),
        ];
    }
}
