<?php

namespace App\Filament\Resources\PracticeCommissions;

use App\Filament\Resources\PracticeCommissions\Pages\CreatePracticeCommission;
use App\Filament\Resources\PracticeCommissions\Pages\EditPracticeCommission;
use App\Filament\Resources\PracticeCommissions\Pages\ListPracticeCommissions;
use App\Filament\Resources\PracticeCommissions\Pages\ListPracticeCompensi;
use App\Filament\Resources\PracticeCommissions\Schemas\PracticeCommissionForm;
use App\Filament\Resources\PracticeCommissions\Tables\PracticeCommissionsTable;
use App\Models\PracticeCommission;
use Filament\Navigation\NavigationItem;  // Add this import at the top
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PracticeCommissionResource extends Resource
{
    protected static ?string $model = PracticeCommission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Provvigioni';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Provvigione';

    protected static ?string $pluralModelLabel = 'Provvigioni';

    protected static string|UnitEnum|null $navigationGroup = 'Pratiche';

    public static function form(Schema $schema): Schema
    {
        return PracticeCommissionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticeCommissionsTable::configure($table);
    }

    public static function getNavigationItems(): array
    {
        return [
            ...parent::getNavigationItems(),
            NavigationItem::make('Provvigioni Attive')
                ->icon('heroicon-o-check-circle')
                ->url(static::getUrl('attive'))
                ->sort(2),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPracticeCommissions::route('/'),
            'create' => CreatePracticeCommission::route('/create'),
            'edit' => EditPracticeCommission::route('/{record}/edit'),
            'attive' => ListPracticeCompensi::route('/attive'),
        ];
    }
}
