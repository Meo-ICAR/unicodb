<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Filament\Resources\CompanyFunctions\Schemas\CompanyFunctionForm;
use App\Filament\Resources\CompanyFunctions\Tables\CompanyFunctionsTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Actions;

class CompanyFunctionsRelationManager extends RelationManager
{
    protected static string $relationship = 'companyFunctions';

    protected static ?string $modelLabel = 'Funzione Aziendale';

    protected static ?string $pluralModelLabel = 'Funzioni Aziendali';

    protected static ?string $title = 'Funzioni Aziendali';

    public function form(Schema $schema): Schema
    {
        return CompanyFunctionForm::configure($schema);
    }

    public function title(string $title): string
    {
        return 'Funzionogramma';
    }

    public function table(Table $table): Table
    {
        return CompanyFunctionsTable::configure($table)
            ->headerActions([
                Actions\CreateAction::make(),
            ]);
    }
}
