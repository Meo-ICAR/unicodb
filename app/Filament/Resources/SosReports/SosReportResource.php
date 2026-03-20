<?php

namespace App\Filament\Resources\SosReports;

use App\Filament\Resources\SosReports\Pages\CreateSosReport;
use App\Filament\Resources\SosReports\Pages\EditSosReport;
use App\Filament\Resources\SosReports\Pages\ListSosReports;
use App\Filament\Resources\SosReports\Pages\ViewSosReport;
use App\Filament\Resources\SosReports\Schemas\SosReportForm;
use App\Filament\Resources\SosReports\Schemas\SosReportInfolist;
use App\Filament\Resources\SosReports\Tables\SosReportsTable;
use App\Filament\Traits\HasChecklistAction;
use App\Models\SosReport;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class SosReportResource extends Resource
{
    protected static ?string $model = SosReport::class;

    // 2. Usa il Trait nella classe della Risorsa

    use HasChecklistAction;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Registro SOS';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Segnalazione Operazione Sospetta';

    protected static ?string $pluralModelLabel = 'Segnalazioni Operazioni Sospette';

    public static function form(Schema $schema): Schema
    {
        return SosReportForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SosReportInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SosReportsTable::configure($table);
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
            'index' => ListSosReports::route('/'),
            'create' => CreateSosReport::route('/create'),
            'view' => ViewSosReport::route('/{record}'),
            'edit' => EditSosReport::route('/{record}/edit'),
        ];
    }
}
