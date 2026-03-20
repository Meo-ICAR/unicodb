<?php

namespace App\Filament\Resources\AuiRecords;

use App\Filament\Resources\AuiRecords\Pages\CreateAuiRecord;
use App\Filament\Resources\AuiRecords\Pages\EditAuiRecord;
use App\Filament\Resources\AuiRecords\Pages\ListAuiRecords;
use App\Filament\Resources\AuiRecords\Pages\ViewAuiRecord;
use App\Filament\Resources\AuiRecords\Schemas\AuiRecordForm;
use App\Filament\Resources\AuiRecords\Schemas\AuiRecordInfolist;
use App\Filament\Resources\AuiRecords\Tables\AuiRecordsTable;
use App\Models\AuiRecord;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class AuiRecordResource extends Resource
{
    protected static ?string $model = AuiRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Registro Antiriciclaggio';

    protected static ?string $modelLabel = 'Antiriciclaggio';

    protected static ?string $pluralModelLabel = 'Antiriciclaggio';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return AuiRecordForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuiRecordInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AuiRecordsTable::configure($table);
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
            'index' => ListAuiRecords::route('/'),
            'create' => CreateAuiRecord::route('/create'),
            'view' => ViewAuiRecord::route('/{record}'),
            'edit' => EditAuiRecord::route('/{record}/edit'),
        ];
    }
}
