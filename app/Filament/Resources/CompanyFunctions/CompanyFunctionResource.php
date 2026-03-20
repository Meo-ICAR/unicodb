<?php

namespace App\Filament\Resources\CompanyFunctions;

use App\Filament\Resources\CompanyFunctions\Pages\CreateCompanyFunction;
use App\Filament\Resources\CompanyFunctions\Pages\EditCompanyFunction;
use App\Filament\Resources\CompanyFunctions\Pages\ListCompanyFunctions;
use App\Filament\Resources\CompanyFunctions\Schemas\CompanyFunctionForm;
use App\Filament\Resources\CompanyFunctions\Tables\CompanyFunctionsTable;
use App\Models\CompanyFunction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class CompanyFunctionResource extends Resource
{
    protected static ?string $model = CompanyFunction::class;

    // protected static ?string $navigationIcon = Heroicon::OutlinedBuildingOffice;

    //    protected static ?string|UnitEnum|null $navigationGroup = 'Organizzazione';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return CompanyFunctionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyFunctionsTable::configure($table);
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
            'index' => ListCompanyFunctions::route('/'),
            'create' => CreateCompanyFunction::route('/create'),
            'edit' => EditCompanyFunction::route('/{record}/edit'),
        ];
    }
}
