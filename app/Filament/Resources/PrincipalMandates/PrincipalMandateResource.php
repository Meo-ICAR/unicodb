<?php

namespace App\Filament\Resources\PrincipalMandates;

use App\Filament\Resources\PrincipalMandates\Pages\CreatePrincipalMandate;
use App\Filament\Resources\PrincipalMandates\Pages\EditPrincipalMandate;
use App\Filament\Resources\PrincipalMandates\Pages\ListPrincipalMandates;
use App\Filament\Resources\PrincipalMandates\Schemas\PrincipalMandateForm;
use App\Filament\Resources\PrincipalMandates\Tables\PrincipalMandatesTable;
use App\Models\PrincipalMandate;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PrincipalMandateResource extends Resource
{
    protected static bool $isScopedToTenant = false;
    protected static ?string $model = PrincipalMandate::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static string|UnitEnum|null $navigationGroup = 'Nucleo Centrale';

    public static function form(Schema $schema): Schema
    {
        return PrincipalMandateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrincipalMandatesTable::configure($table);
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
            'index' => ListPrincipalMandates::route('/'),
            'create' => CreatePrincipalMandate::route('/create'),
            'edit' => EditPrincipalMandate::route('/{record}/edit'),
        ];
    }
}
