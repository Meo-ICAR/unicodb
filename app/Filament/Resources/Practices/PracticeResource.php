<?php

namespace App\Filament\Resources\Practices;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Practices\Pages\CreatePractice;
use App\Filament\Resources\Practices\Pages\EditPractice;
use App\Filament\Resources\Practices\Pages\ListPracticeOAMs;
use App\Filament\Resources\Practices\Pages\ListPractices;
use App\Filament\Resources\Practices\RelationManagers\ClientsRelationManager;
use App\Filament\Resources\Practices\RelationManagers\PracticeCommissionsRelationManager;
use App\Filament\Resources\Practices\Schemas\PracticeForm;
use App\Filament\Resources\Practices\Tables\PracticeOAMsTable;
use App\Filament\Resources\Practices\Tables\PracticesTable;
use App\Models\Practice;
use Filament\Navigation\NavigationItem;  // Add this import at the top
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PracticeResource extends Resource
{
    protected static ?string $model = Practice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|UnitEnum|null $navigationGroup = 'Pratiche';

    protected static ?string $modelLabel = 'Pratica';

    protected static ?string $pluralModelLabel = 'Pratiche';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return PracticeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PracticesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ClientsRelationManager::class,
            PracticeCommissionsRelationManager::class,
            DocumentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPractices::route('/'),
            'create' => CreatePractice::route('/create'),
            'edit' => EditPractice::route('/{record}/edit'),
        ];
    }

    /*
     * public static function getNavigationItems(): array
     * {
     *     return [
     *         ...parent::getNavigationItems(),
     *         NavigationItem::make('OAM Vigilanza')
     *             ->icon('heroicon-o-check-circle')
     *             ->url(static::getUrl('oam'))
     *             ->sort(2),
     *     ];
     * }
     */
}
