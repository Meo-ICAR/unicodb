<?php

namespace App\Filament\Resources\PrincipalEmployees;

use App\Filament\Resources\PrincipalEmployees\Pages\CreatePrincipalEmployee;
use App\Filament\Resources\PrincipalEmployees\Pages\EditPrincipalEmployee;
use App\Filament\Resources\PrincipalEmployees\Pages\ListPrincipalEmployees;
use App\Filament\Resources\PrincipalEmployees\Schemas\PrincipalEmployeeForm;
use App\Filament\Resources\PrincipalEmployees\Tables\PrincipalEmployeesTable;
use App\Models\PrincipalEmployee;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrincipalEmployeeResource extends Resource
{
    protected static ?string $model = PrincipalEmployee::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static bool $isScopedToTenant = false;

    public static function form(Schema $schema): Schema
    {
        return PrincipalEmployeeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrincipalEmployeesTable::configure($table);
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
            'index' => ListPrincipalEmployees::route('/'),
            'create' => CreatePrincipalEmployee::route('/create'),
            'edit' => EditPrincipalEmployee::route('/{record}/edit'),
        ];
    }
}
