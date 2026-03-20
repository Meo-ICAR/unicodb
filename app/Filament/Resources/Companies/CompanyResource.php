<?php

namespace App\Filament\Resources\Companies;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\RelationManagers\WebsitesRelationManager;
use App\Filament\Resources\Companies\Pages\CreateCompany;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Filament\Resources\Companies\Pages\ListCompanies;
use App\Filament\Resources\Companies\RelationManagers\BranchesRelationManager;
use App\Filament\Resources\Companies\RelationManagers\CompanyClientsRelationManager;
use App\Filament\Resources\Companies\RelationManagers\CompanyFunctionsRelationManager;
use App\Filament\Resources\Companies\RelationManagers\SendersRelationManager;
use App\Filament\Resources\Companies\RelationManagers\SoftwareApplicationsRelationManager;
use App\Filament\Resources\Companies\Schemas\CompanyForm;
use App\Filament\Resources\Companies\Tables\CompaniesTable;
use App\Models\Company;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static bool $isScopedToTenant = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Azienda';

    protected static ?int $navigationSort = 8;

    protected static ?string $modelLabel = 'Azienda';

    protected static ?string $pluralModelLabel = 'Aziende';

    protected static string|UnitEnum|null $navigationGroup = 'Organizzazione';

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompaniesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,
            CompanyClientsRelationManager::class,
            CompanyFunctionsRelationManager::class,
            SendersRelationManager::class,
            WebsitesRelationManager::class,
            BranchesRelationManager::class,
            SoftwareApplicationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}
