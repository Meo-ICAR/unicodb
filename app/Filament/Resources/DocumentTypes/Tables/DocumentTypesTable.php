<?php

namespace App\Filament\Resources\DocumentTypes\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocumentTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Codice')
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('is_person')
                    ->label('PF')
                    ->boolean()
                    ->tooltip('Persona Fisica'),
                IconColumn::make('is_agent')
                    ->label('Agenti')
                    ->boolean()
                    ->tooltip('Applicabile agli Agenti'),
                IconColumn::make('is_principal')
                    ->label('Principal')
                    ->boolean()
                    ->tooltip('Applicabile ai Principal'),
                IconColumn::make('is_client')
                    ->label('Client')
                    ->boolean()
                    ->tooltip('Applicabile ai Client'),
                IconColumn::make('is_practice')
                    ->label('Pratiche')
                    ->boolean()
                    ->tooltip('Applicabile alle Pratiche'),
                IconColumn::make('is_company')
                    ->label('Company')
                    ->boolean()
                    ->tooltip('Documento Company'),
                IconColumn::make('is_signed')
                    ->label('Firma')
                    ->boolean()
                    ->tooltip('Richiede Firma'),
                IconColumn::make('is_monitored')
                    ->label('Monitor')
                    ->boolean()
                    ->tooltip('Monitoraggio Scadenza'),
                TextColumn::make('duration')
                    ->label('Durata')
                    ->suffix(' giorni')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('emitted_by')
                    ->label('Ente')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('set_target_filters')
                        ->label('Imposta Filtri Target')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->form([
                            Section::make('Seleziona Destinatari')
                                ->description('Imposta massivamente i filtri target per i documenti selezionati')
                                ->schema([
                                    Toggle::make('is_agent')
                                        ->label('Agenti')
                                        ->helperText('Applicabile agli agenti'),
                                    Toggle::make('is_principal')
                                        ->label('Principal')
                                        ->helperText('Applicabile ai mandanti'),
                                    Toggle::make('is_client')
                                        ->label('Client')
                                        ->helperText('Applicabile ai clienti'),
                                    Toggle::make('is_practice')
                                        ->label('Pratiche')
                                        ->helperText('Applicabile alle pratiche'),
                                    Toggle::make('is_company')
                                        ->label('Company')
                                        ->helperText('Documento aziendale generale'),
                                ]),
                        ])
                        ->action(function (array $data, $records) {
                            $count = 0;
                            foreach ($records as $record) {
                                $record->update([
                                    'is_agent' => $data['is_agent'] ?? $record->is_agent,
                                    'is_principal' => $data['is_principal'] ?? $record->is_principal,
                                    'is_client' => $data['is_client'] ?? $record->is_client,
                                    'is_practice' => $data['is_practice'] ?? $record->is_practice,
                                    'is_company' => $data['is_company'] ?? $record->is_company,
                                ]);
                                $count++;
                            }

                            Notification::make()
                                ->success()
                                ->title('Filtri Target Aggiornati')
                                ->body("Aggiornati {$count} tipi documento")
                                ->send();
                        }),
                    BulkAction::make('clear_target_filters')
                        ->label('Rimuovi Filtri Target')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                $record->update([
                                    'is_agent' => false,
                                    'is_principal' => false,
                                    'is_client' => false,
                                    'is_practice' => false,
                                    'is_company' => false,
                                ]);
                                $count++;
                            }

                            Notification::make()
                                ->success()
                                ->title('Filtri Target Rimossi')
                                ->body("Rimossi filtri da {$count} tipi documento")
                                ->send();
                        }),
                ]),
            ]);
    }
}
