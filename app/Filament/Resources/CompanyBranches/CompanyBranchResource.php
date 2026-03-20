<?php

namespace App\Filament\Resources\CompanyBranches;

use App\Filament\Resources\CompanyBranches\Pages\CreateCompanyBranch;
use App\Filament\Resources\CompanyBranches\Pages\EditCompanyBranch;
use App\Filament\Resources\CompanyBranches\Pages\ListCompanyBranches;
use App\Filament\Resources\CompanyBranches\Schemas\CompanyBranchForm;
use App\Filament\Resources\CompanyBranches\Tables\CompanyBranchesTable;
use App\Models\CompanyBranch;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class CompanyBranchResource extends Resource
{
    protected static ?string $model = CompanyBranch::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|UnitEnum|null $navigationGroup = 'Impostazioni';

    public static function form(Schema $schema): Schema
    {
        return CompanyBranchForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyBranchesTable::configure($table);
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
            'index' => ListCompanyBranches::route('/'),
            'create' => CreateCompanyBranch::route('/create'),
            'edit' => EditCompanyBranch::route('/{record}/edit'),
        ];
    }
}
