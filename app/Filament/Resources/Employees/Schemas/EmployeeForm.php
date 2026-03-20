<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nominativo'),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->tel(),
                Select::make('role_title')
                    ->label('Ruolo')
                    ->options([
                        'Amministratore' => 'Amministratore',
                        'Operatore' => 'Operatore',
                        'Consulente' => 'Consulente',
                    ]),
                Select::make('company_branch_id')
                    ->label('Sede')
                    ->relationship('companyBranch', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('coordinated_by_id')
                    ->label('Coordinato da')
                    ->relationship('coordinatedBy', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Seleziona un coordinatore della stessa sede'),
                TextInput::make('department')
                    ->label('Dipartimento')
                    ->required(),
                Select::make('employment_type_id')
                    ->label('Tipo di Impiego')
                    ->relationship('employmentType', 'name')
                    ->searchable()
                    ->preload(),
                DatePicker::make('hire_date')
                    ->label('Data Assunzione'),
                DatePicker::make('termination_date')
                    ->label('Data Cessazione'),
            ]);
    }
}
