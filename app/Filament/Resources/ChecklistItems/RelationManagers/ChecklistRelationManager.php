<?php

namespace App\Filament\Resources\ChecklistItems\RelationManagers;

use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChecklistRelationManager extends RelationManager
{
    protected static string $relationship = 'checklist';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_id'),
                TextInput::make('name'),
                TextInput::make('code'),
                Select::make('type')
                    ->options(['loan_management' => 'Loan management', 'audit' => 'Audit']),
                Textarea::make('description')
                    ->columnSpanFull(),
                Select::make('principal_id')
                    ->relationship('principal', 'name'),
                Select::make('document_type_id')
                    ->relationship('documentType', 'name'),
                Toggle::make('is_practice'),
                Toggle::make('is_audit'),
                Toggle::make('is_template'),
                TextInput::make('target_type'),
                TextInput::make('target_id')
                    ->numeric(),
                Select::make('document_id')
                    ->relationship('document', 'name'),
                Select::make('status')
                    ->options(['da_compilare' => 'Da compilare', 'in_corso' => 'In corso', 'completata' => 'Completata'])
                    ->default('da_compilare'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('company_id')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge(),
                TextColumn::make('principal.name')
                    ->searchable(),
                TextColumn::make('documentType.name')
                    ->searchable(),
                IconColumn::make('is_practice')
                    ->boolean(),
                IconColumn::make('is_audit')
                    ->boolean(),
                IconColumn::make('is_template')
                    ->boolean(),
                TextColumn::make('target_type')
                    ->searchable(),
                TextColumn::make('target_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('document.name')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AttachAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DetachAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
