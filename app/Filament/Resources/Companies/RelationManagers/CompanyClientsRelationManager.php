<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\CompanyClients\Tables\CompanyClientsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CompanyClientsRelationManager extends RelationManager
{
    protected static string $relationship = 'companyClients';

    protected static ?string $modelLabel = 'Consulente';

    protected static ?string $pluralModelLabel = 'Consulenti';

    protected static ?string $title = 'Consulenti';

    public function table(Table $table): Table
    {
        return CompanyClientsTable::configure($table);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }
}
