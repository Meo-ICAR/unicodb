<?php

namespace App\Filament\Resources\PracticeOams;

use App\Filament\Resources\PracticeOams\Pages\CreatePracticeOam;
use App\Filament\Resources\PracticeOams\Pages\EditPracticeOam;
use App\Filament\Resources\PracticeOams\Pages\ListPracticeOams;
use App\Filament\Resources\PracticeOams\Schemas\PracticeOamForm;
use App\Filament\Resources\PracticeOams\Tables\PracticeOamsTable;
use App\Models\PracticeOam;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PracticeOamResource extends Resource
{
    protected static ?string $model = PracticeOam::class;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'OAM Vigilanza';

    protected static ?string $modelLabel = 'OAM Vigilanza';

    protected static ?string $pluralModelLabel = 'OAM Vigilanza';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return PracticeOamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticeOamsTable::configure($table);
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
            'index' => ListPracticeOams::route('/'),
            'create' => CreatePracticeOam::route('/create'),
            'edit' => EditPracticeOam::route('/{record}/edit'),
        ];
    }
}
