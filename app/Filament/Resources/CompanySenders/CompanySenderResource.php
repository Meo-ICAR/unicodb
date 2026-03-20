<?php

namespace App\Filament\Resources\CompanySenders;

use App\Filament\Resources\CompanySenders\Pages\CreateCompanySender;
use App\Filament\Resources\CompanySenders\Pages\EditCompanySender;
use App\Filament\Resources\CompanySenders\Pages\ListCompanySenders;
use App\Filament\Resources\CompanySenders\Schemas\CompanySenderForm;
use App\Filament\Resources\CompanySenders\Tables\CompanySendersTable;
use App\Models\CompanySender;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;

class CompanySenderResource extends Resource
{
    protected static ?string $model = CompanySender::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return CompanySenderForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanySendersTable::configure($table);
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
            'index' => ListCompanySenders::route('/'),
            'create' => CreateCompanySender::route('/create'),
            'edit' => EditCompanySender::route('/{record}/edit'),
        ];
    }
}
