<?php

namespace App\Filament\Resources\CompanyClients;

use App\Filament\Resources\CompanyClients\Pages\CreateCompanyClient;
use App\Filament\Resources\CompanyClients\Pages\EditCompanyClient;
use App\Filament\Resources\CompanyClients\Pages\ListCompanyClients;
use App\Filament\Resources\CompanyClients\Schemas\CompanyClientForm;
use App\Filament\Resources\CompanyClients\Tables\CompanyClientsTable;
use App\Models\CompanyClient;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;

class CompanyClientResource extends Resource
{
    protected static ?string $model = CompanyClient::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CompanyClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyClientsTable::configure($table);
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
            'index' => ListCompanyClients::route('/'),
            'create' => CreateCompanyClient::route('/create'),
            'edit' => EditCompanyClient::route('/{record}/edit'),
        ];
    }
}
