<?php

namespace App\Filament\Resources\Principals\Tables;

use App\Filament\Traits\HasChecklistAction;  // 1. Importa il namespace
use App\Models\Abi;  // Assicurati che il modello Abi esista
use App\Models\Principal;  // Assicurati che il modello Abi esista
use App\Services\ChecklistService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Excel;

class PrincipalsTable
{
    use HasChecklistAction;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('abi')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_dummy')
                    ->label('Fittizio')
                    ->boolean()
                    ->trueIcon('heroicon-s-exclamation-triangle')
                    ->falseIcon('heroicon-o-building-office')
                    ->color(fn($state) => $state ? 'warning' : 'success')
                    ->tooltip(fn($record) => $record->is_dummy ? 'Mandante fittizio / non convenzionato' : 'Mandante convenzionato'),
                TextColumn::make('stipulated_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('dismissed_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('vat_number')
                    ->label('CF / Partita IVA')
                    ->searchable(),
                TextColumn::make('oam')
                    ->searchable(),
                TextColumn::make('principal_type')
                    ->label('Tipo Mandante')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'no' => 'Non Specificato',
                        'banca' => 'Banca',
                        'assicurazione' => 'Assicurazione',
                        'agente' => 'Agente',
                        'agente_captive' => 'Agente Captive',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'no' => 'gray',
                        'banca' => 'blue',
                        'assicurazione' => 'green',
                        'agente' => 'purple',
                        'agente_captive' => 'orange',
                        default => 'gray',
                    }),
                TextColumn::make('ivass')
                    ->searchable(),
                TextColumn::make('ivass_section')
                    ->label('Sezione IVASS')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                IconColumn::make('is_exclusive')
                    ->boolean(),
                IconColumn::make('is_reported')
                    ->label('Segnalazione')
                    ->boolean()
                    ->trueIcon('heroicon-s-hand-raised')
                    ->falseIcon('heroicon-o-hand-raised')
                    ->color(fn($state) => $state ? 'success' : 'gray')
                    ->tooltip(fn($record) => $record->is_reported ? 'Accordi di segnalazione attivi' : 'Nessun accordo di segnalazione'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('website')
                    ->label('Sito Web')
                    ->url(fn($record) => $record->website)
                    ->openUrlInNewTab()
                    ->toggleable(),
                TextColumn::make('portalsite')
                    ->label('Portale')
                    ->url(fn($record) => $record->portalsite)
                    ->openUrlInNewTab()
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('senza_abi')
                    ->label('Senza ABI')
                    ->query(fn(Builder $query): Builder => $query->whereNull('principals.abi'))
                    ->indicator('ABI Mancante'),  // Mostra un badge sopra la tabella quando è attivo
                Filter::make('con_segnalazione')
                    ->label('Con Accordi di Segnalazione')
                    ->query(fn(Builder $query): Builder => $query->where('is_reported', true))
                    ->indicator('Segnalazione'),
            ])
            ->recordActions([
                // EditAction::make(),
                ...self::getChecklistActions(
                    code: 'BANK_AUDIT',  // <-- Il 'code' esatto presente nel tuo DB
                    label: 'Audit',
                    // icon: 'heroicon-o-clipboard-document-check'
                ),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkActionGroup::make([
                    //    DeleteBulkAction::make(),
                    BulkAction::make('assegnaAbi')
                        ->label('Assegna ABI ai Mandanti')
                        ->icon('heroicon-o-banknotes')
                        ->color('warning')
                        // Definiamo il form che appare nel modal dopo il click
                        ->form([
                            Select::make('abi')
                                ->label('Seleziona ABI')
                                ->options(Abi::query()->pluck('name', 'abi'))  // Sostituisci nome_banca col tuo campo
                                ->searchable()
                                ->required(),
                        ])
                        // Logica di esecuzione sui record selezionati
                        ->action(function (Collection $records, array $data): void {
                            $abiId = $data['abi'];

                            // Recuperiamo gli ID univoci dei Principal dai record selezionati
                            // (Necessario perché la query raggruppata potrebbe avere duplicati di ID per diversi prodotti)
                            $principalIds = $records->pluck('id')->unique();

                            // Aggiornamento massivo sul database
                            Principal::whereIn('id', $principalIds)
                                ->update(['abi' => $abiId]);
                        })
                        ->deselectRecordsAfterCompletion()  // Pulisce la selezione a fine operazione
                        ->requiresConfirmation()
                        ->modalHeading('Assegnazione Massiva ABI')
                        ->modalDescription("Seleziona l'istituto da associare a tutti i mandanti selezionati.")
                        ->modalSubmitActionLabel('Conferma Assegnazione'),
                ]),
            ]);
    }
}
