<?php

namespace App\Filament\Resources\Principals;

use App\Filament\RelationManagers\DocumentsRelationManager;
use App\Filament\RelationManagers\WebsitesRelationManager;
use App\Filament\Resources\Principals\Imports\PrincipalsImport;
use App\Filament\Resources\Principals\Pages\CreatePrincipal;
use App\Filament\Resources\Principals\Pages\EditPrincipal;
use App\Filament\Resources\Principals\Pages\ListPrincipals;
use App\Filament\Resources\Principals\Pages\ListPrincipalScopes;
use App\Filament\Resources\Principals\RelationManagers\ContactsRelationManager;
use App\Filament\Resources\Principals\RelationManagers\EmployeesRelationManager;
use App\Filament\Resources\Principals\RelationManagers\PrincipalMandatesRelationManager;
use App\Filament\Resources\Principals\RelationManagers\PrincipalScopesRelationManager;
use App\Filament\Resources\Principals\RelationManagers\SalesInvoicesRelationManager;
use App\Filament\Resources\Principals\Schemas\PrincipalForm;
use App\Filament\Resources\Principals\Tables\PrincipalsTable;
use App\Filament\Traits\HasChecklistAction;
use App\Models\Principal;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use BackedEnum;
use UnitEnum;

class PrincipalResource extends Resource
{
    protected static ?string $model = Principal::class;

    // 2. Usa il Trait nella classe della Risorsa

    use HasChecklistAction;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Organizzazione';

    protected static ?string $navigationLabel = 'Mandanti';

    protected static ?string $modelLabel = 'Mandante';

    protected static ?string $pluralModelLabel = 'Mandanti';

    public static function form(Schema $schema): Schema
    {
        return PrincipalForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrincipalsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ContactsRelationManager::class,
            PrincipalMandatesRelationManager::class,
            PrincipalScopesRelationManager::class,
            DocumentsRelationManager::class,
            WebsitesRelationManager::class,
            EmployeesRelationManager::class,
            SalesInvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrincipals::route('/'),
            'scopes' => ListPrincipalScopes::route('/scopes'),
            'create' => CreatePrincipal::route('/create'),
            'edit' => EditPrincipal::route('/{record}/edit'),
        ];
    }
}
