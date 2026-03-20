<?php

namespace App\Filament\Resources\Practices\Tables;

use App\Filament\Imports\PracticesImporter;
use App\Filament\Traits\HasChecklistAction;  // 1. Importa il namespace
use App\Models\Practice;
use App\Models\PracticeStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Excel;

class PracticesTable
{
    use HasChecklistAction;

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['principal', 'agent', 'practiceScope', 'practiceStatus', 'clientMandate']))
            ->columns([
                TextColumn::make('tipo_prodotto')
                    ->label('Tipo Prodotto')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Assente'),
                TextColumn::make('principal.name')
                    ->label('Mandante')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun mandante'),
                TextColumn::make('inserted_at')
                    ->label('Data Inserimento')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Non definita'),
                TextColumn::make('sended_at')
                    ->label('Istruttoria')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('approved_at')
                    ->label('Delibera')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('erogated_at')
                    ->label('Erogazione')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('perfected_at')
                    ->label('Perfezionata')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('invoice_at')
                    ->label('Fatturazione')
                    ->date()
                    ->sortable()
                    ->placeholder('Non definita'),
                IconColumn::make('is_invoiced')
                    ->label('Fatturata')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('clients_names')
                    ->label('Contraenti')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun cliente'),
                TextColumn::make('agent.name')
                    ->label('Agente')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun agente'),
                TextColumn::make('practiceStatus.name')
                    ->label('Stato Pratica')
                    ->badge()
                    ->color(fn($record) => $record->practiceStatus?->color ?? 'gray')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessuno stato'),
                TextColumn::make('stato_pratica')
                    ->label('Stato Originale')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Nessuno stato originale'),
                TextColumn::make('name')
                    ->label('Nome Pratica')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('CRM_code')
                    ->label('Codice CRM')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('principal_code')
                    ->label('Codice Mandante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Importo')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('net')
                    ->label('Netto')
                    ->money('EUR')
                    ->sortable(),
                TextColumn::make('practiceScope.name')
                    ->label('Ambito')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Nessun ambito'),
                TextColumn::make('statoproforma')
                    ->label('Stato Proforma')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'Inserito' => 'blue',
                        'Sospeso' => 'yellow',
                        'Annullato' => 'red',
                        'Inviato' => 'green',
                        'Abbinato' => 'purple',
                        default => 'gray',
                    })
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Nessuno stato proforma'),
                TextColumn::make('rejected_at')
                    ->label('Data Rifiuto')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Non definita'),
                TextColumn::make('status_at')
                    ->label('Data Stato')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Non definita'),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn($state) => PracticeStatus::where('name', $state)->value('color') ?? 'gray')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('brokerage_fee')
                    ->label('Provvigione')
                    ->money('EUR')
                    ->sortable()
                    ->placeholder('Non definita'),
                TextColumn::make('rejected_reason')
                    ->label('Causale Rifiuto')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Nessuna causale'),
                IconColumn::make('is_active')
                    ->label('Attiva')
                    ->boolean(),
                IconColumn::make('isPerfected')
                    ->label('Perfezionata')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn($record) => $record->isPerfected()
                        ? ($record->perfected_at ? 'Perfezionata il: ' . $record->perfected_at->format('d/m/Y') : 'Perfezionata')
                        : 'Non perfezionata'),
                IconColumn::make('isWorking')
                    ->label('In Lavorazione')
                    ->boolean()
                    ->trueIcon('heroicon-s-clock')
                    ->falseIcon('heroicon-o-pause-circle')
                    ->color(fn($state) => $state ? 'warning' : 'gray')
                    ->tooltip(fn($record) => $record->isWorking() ? 'In lavorazione' : 'Non in lavorazione'),
                IconColumn::make('isRejected')
                    ->label('Respinta')
                    ->boolean()
                    ->trueIcon('heroicon-s-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->color(fn($state) => $state ? 'danger' : 'success')
                    ->tooltip(fn($record) => $record->isRejected() ? 'Respinta' : 'Non respinta'),
            ])
            ->filters([
                SelectFilter::make('Istruttoria')
                    ->label('Inviate in Istruttoria')
                    ->options([
                        'stipulated' => 'Inviati in Istruttoria',
                        'not_stipulated' => 'Non Inviati in Istruttoria',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'stipulated',
                                fn(Builder $query): Builder => $query->whereNotNull('sended_at'),
                            )
                            ->when(
                                $data['value'] === 'not_stipulated',
                                fn(Builder $query): Builder => $query->whereNull('sended_at'),
                            );
                    }),
                SelectFilter::make('Deliberati')
                    ->label('Pratiche deliberate')
                    ->options([
                        'stipulated' => 'Deliberati',
                        'not_stipulated' => 'Non Deliberati',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'stipulated',
                                fn(Builder $query): Builder => $query->whereNotNull('approved_at'),
                            )
                            ->when(
                                $data['value'] === 'not_stipulated',
                                fn(Builder $query): Builder => $query->whereNull('approved_at'),
                            );
                    }),
                SelectFilter::make('Erogati')
                    ->label('Pratiche erogate')
                    ->options([
                        'stipulated' => 'Erogati',
                        'not_stipulated' => 'Non Erogati',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'stipulated',
                                fn(Builder $query): Builder => $query->whereNotNull('erogated_at'),
                            )
                            ->when(
                                $data['value'] === 'not_stipulated',
                                fn(Builder $query): Builder => $query->whereNull('erogated_at'),
                            );
                    }),
                SelectFilter::make('Perfezionate')
                    ->label('Pratiche perfezionate')
                    ->options([
                        'stipulated' => 'Perfezionate',
                        'not_stipulated' => 'Non Perfezionate',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'stipulated',
                                fn(Builder $query): Builder => $query->whereNotNull('perfected_at'),
                            )
                            ->when(
                                $data['value'] === 'not_stipulated',
                                fn(Builder $query): Builder => $query->whereNull('perfected_at'),
                            );
                    }),
                TernaryFilter::make('is_invoiced')
                    ->label('Fatturato')
                    ->placeholder('Tutti gli stati')
                    ->trueLabel('Almeno una provvigione fatturata')
                    ->falseLabel('Nessuna provvigione fatturata')
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('practiceCommissions', function ($commissionQuery) {
                            $commissionQuery->whereNotNull('invoice_at');
                        }),
                        false: fn(Builder $query) => $query->whereDoesntHave('practices.practiceCommissions', function ($commissionQuery) {
                            $commissionQuery->whereNotNull('invoice_at');
                        })
                    ),
                SelectFilter::make('tipo_prodotto')
                    ->label('Filtra per Tipo')
                    ->multiple()  // Abilita la selezione multipla
                    ->options(
                        // Recupera i valori unici della colonna 'type' dal database
                        fn() => Practice::query()
                            ->whereNotNull('tipo_prodotto')  // Esclude null values
                            ->pluck('tipo_prodotto', 'tipo_prodotto')  // 'valore' => 'etichetta'
                            ->sort()
                            ->toArray()
                    )
                    ->searchable()  // Opzionale: aggiunge una barra di ricerca nel dropdown
            ])
            ->recordActions([
                ...self::getChecklistActions(
                    code: 'Cessione',  // <-- Il 'code' esatto presente nel tuo DB
                    label: 'Cessione'
                    // icon: 'heroicon-o-clipboard-document-check'
                ),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([]),
            ]);
    }
}
