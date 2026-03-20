<?php

namespace App\Filament\Resources\ComplianceViolations\Tables;

use App\Filament\Resources\ComplianceViolations\ComplianceViolationResource;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\DateTimePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

class ComplianceViolationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['company', 'user', 'violatable', 'resolvedBy']))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('violation_type')
                    ->label('Tipo Violazione')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'accesso_non_autorizzato' => 'Accesso Non Autorizzato',
                        'kyc_scaduto' => 'KYC Scaduto',
                        'forzatura_stato' => 'Forzatura Stato',
                        'data_breach' => 'Data Breach',
                        'violazione_privacy' => 'Violazione Privacy',
                        'accesso_abusivo' => 'Accesso Abusivo',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->badge()
                    ->sortable(),
                TextColumn::make('severity')
                    ->label('Gravità')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'basso' => 'Basso',
                        'medio' => 'Medio',
                        'alto' => 'Alto',
                        'critico' => 'Critico',
                        default => ucfirst($state),
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'basso' => 'success',
                        'medio' => 'warning',
                        'alto' => 'danger',
                        'critico' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Descrizione')
                    ->limit(100)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('violatable_type')
                    ->label('Tipo Entità')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\Models\Client' => 'Cliente',
                        'App\Models\Dossier' => 'Dossier',
                        'App\Models\Practice' => 'Pratica',
                        'App\Models\Document' => 'Documento',
                        'App\Models\Checklist' => 'Checklist',
                        default => $state,
                    })
                    ->badge()
                    ->sortable(),
                TextColumn::make('violatable.name')
                    ->label('Entità Violata')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Utente')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sistema'),
                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('resolved_at')
                    ->label('Risolta Il')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Non risolta'),
                TextColumn::make('resolvedBy.name')
                    ->label('Risolta Da')
                    ->searchable()
                    ->sortable()
                    ->placeholder('N/A'),
                TextColumn::make('created_at')
                    ->label('Creato Il')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('create')
                    ->label('Nuova Violazione')
                    ->icon('heroicon-o-plus')
                    ->url(fn() => ComplianceViolationResource::getUrl('create')),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('resolve')
                    ->label('Risolvi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Risolvi Violazione')
                    ->modalDescription('Sei sicuro di voler contrassegnare questa violazione come risolta?')
                    ->form([
                        /*
                         * DateTimePicker::make('resolved_at')
                         *     ->label('Data Risoluzione')
                         *     ->required()
                         *     ->default(now()),
                         */
                        Textarea::make('resolution_notes')
                            ->label('Note Risoluzione')
                            ->required(),
                    ])
                    ->action(function (array $data, Model $record): void {
                        $record->update([
                            'resolved_at' => $data['resolved_at'],
                            'resolution_notes' => $data['resolution_notes'],
                            'resolved_by' => auth()->id(),
                        ]);
                    })
                    ->visible(fn(Model $record): bool => is_null($record->resolved_at)),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
