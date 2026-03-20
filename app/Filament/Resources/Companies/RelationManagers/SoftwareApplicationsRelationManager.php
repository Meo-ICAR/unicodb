<?php

namespace App\Filament\Resources\Companies\RelationManagers;

use App\Models\SoftwareApplication;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;

class SoftwareApplicationsRelationManager extends RelationManager
{
    protected static string $relationship = 'softwareApplications';

    protected static ?string $title = 'Software Applicativi';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nome Software')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('provider_name')
                    ->label('Produttore')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'ATTIVO' => 'success',
                        'SOSPESO' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Associa Software')
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('status')
                            ->options([
                                'ATTIVO' => 'Attivo',
                                'SOSPESO' => 'Sospeso',
                            ])
                            ->default('ATTIVO')
                            ->required(),
                        Textarea::make('notes')
                            ->label('Note Aziendali'),
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->label('Modifica Associazione')
                    ->form([
                        Select::make('status')
                            ->options([
                                'ATTIVO' => 'Attivo',
                                'SOSPESO' => 'Sospeso',
                            ])
                            ->required(),
                        Textarea::make('notes')
                            ->label('Note Aziendali'),
                    ]),
                \Filament\Actions\DetachAction::make()
                    ->label('Rimuovi'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
