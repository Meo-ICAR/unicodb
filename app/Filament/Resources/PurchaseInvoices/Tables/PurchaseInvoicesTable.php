<?php

namespace App\Filament\Resources\PurchaseInvoices\Tables;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Principal;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseInvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')
                    ->label('Doc. n.')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('document_date')
                    ->label('Del')
                    ->date()
                    ->sortable(),
                TextColumn::make('Supplier')
                    ->label('Fornitore')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(Sum::make()->money('EUR')),
                TextColumn::make('amount_including_vat')
                    ->label('Amount incl. VAT')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(Sum::make()->money('EUR')),
                TextColumn::make('residual_amount')
                    ->label('Residual')
                    ->money('EUR')
                    ->sortable()
                    ->summarize(Sum::make()->money('EUR'))
                    ->color(function ($state) {
                        return $state > 0 ? 'warning' : 'success';
                    }),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color(function ($state, $record) {
                        if (!$state || $record->closed)
                            return null;
                        return $state->isPast() ? 'danger' : null;
                    }),
                IconColumn::make('closed')
                    ->label('Closed')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('cancelled')
                    ->label('Cancelled')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('invoiceable_type')
                    ->label('Attached To')
                    ->options([
                        'App\Models\Client' => 'Client',
                        'App\Models\Agent' => 'Agent',
                        'App\Models\Principal' => 'Principal',
                    ]),
                Filter::make('invoiceable_id')
                    ->label('Senza attach')
                    ->query(fn($query) => $query->whereNull('invoiceable_id')),
                Filter::make('open_invoices')
                    ->label('Open Invoices')
                    ->query(fn($query) => $query->where('closed', false)),
                Filter::make('overdue')
                    ->label('Overdue')
                    ->query(function ($query) {
                        return $query
                            ->where('closed', false)
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', now());
                    }),
            ])
            ->actions([
                //   EditAction::make(),
                Action::make('attach_to_model')
                    ->label('Associa')
                    ->icon('heroicon-o-link')
                    ->color('success')
                    ->visible(fn($record) => is_null($record->invoiceable_id))
                    ->form([
                        Select::make('invoiceable_type')
                            ->label('Tipo')
                            ->options([
                                'App\Models\Client' => 'Consulenti',
                                'App\Models\Agent' => 'Agenti',
                                //  'App\Models\Principal' => 'Principal',
                            ])
                            ->required()
                            ->reactive(),
                        TextInput::make('invoiceable_name')
                            ->label('Nome Record (cerca o crea nuovo)')
                            ->default(fn($record) => $record->supplier)
                            ->required()
                            ->helperText('Inserisci un nome esistente o uno nuovo per creare automaticamente il record'),
                    ])
                    ->action(function (array $data, $record) {
                        $invoiceableId = null;
                        $searchTerm = $data['invoiceable_name'] ?? null;

                        // Se è vuoto o null, crea il record
                        if (is_null($searchTerm) || $searchTerm === '') {
                            $newRecord = match ($data['invoiceable_type']) {
                                'App\Models\Client' => \App\Models\Client::create(['name' => $record->supplier . date('Y-m-d H:i'),
                                    'vat_number' => $record->vat_number]),
                                'App\Models\Agent' => \App\Models\Agent::create(['name' => $record->supplier . date('Y-m-d H:i'),
                                    'vat_number' => $record->vat_number]),
                                'App\Models\Principal' => \App\Models\Principal::create(['name' => $record->supplier . date('Y-m-d H:i'),
                                    'vat_number' => $record->vat_number]),
                                default => null
                            };

                            if ($newRecord) {
                                $invoiceableId = $newRecord->id;
                            }
                        } else {
                            // Verifica se esiste un record con questo nome
                            $existingRecord = match ($data['invoiceable_type']) {
                                'App\Models\Client' => Client::where('name', $searchTerm)->first(),
                                'App\Models\Agent' => Agent::where('name', $searchTerm)->first(),
                                'App\Models\Principal' => Principal::where('name', $searchTerm)->first(),
                                default => null
                            };

                            if ($existingRecord) {
                                $invoiceableId = $existingRecord->id;
                            } else {
                                // Crea nuovo record con il nome cercato
                                $newRecord = match ($data['invoiceable_type']) {
                                    'App\Models\Client' => Client::create(['name' => $searchTerm,
                                        'vat_number' => $record->vat_number]),
                                    'App\Models\Agent' => Agent::create(['name' => $searchTerm,
                                        'vat_number' => $record->vat_number]),
                                    'App\Models\Principal' => Principal::create(['name' => $searchTerm,
                                        'vat_number' => $record->vat_number]),
                                    default => null
                                };

                                if ($newRecord) {
                                    $invoiceableId = $newRecord->id;
                                }
                            }
                        }

                        if ($invoiceableId) {
                            // Prima aggiorna il record corrente
                            $record->update([
                                'invoiceable_type' => $data['invoiceable_type'],
                                'invoiceable_id' => $invoiceableId,
                            ]);

                            // Poi associa tutte le altre fatture dello stesso supplier senza attach
                            $updatedCount = \App\Models\PurchaseInvoice::where('supplier', $record->supplier)
                                ->whereNull('invoiceable_id')
                                ->update([
                                    'invoiceable_type' => $data['invoiceable_type'],
                                    'invoiceable_id' => $invoiceableId,
                                ]);

                            $totalUpdated = $updatedCount + 1;  // +1 per il record corrente

                            $actionText = (is_null($searchTerm) || $searchTerm === '') ? 'creato e associato' : 'associato';
                            \Filament\Notifications\Notification::make()
                                ->title('Fatture associate')
                                ->body("{$totalUpdated} fatture del supplier '{$record->supplier}' {$actionText} correttamente")
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->headerActions([])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_as_closed')
                        ->label('Chiudi Selezionati')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $count = $records->where('closed', false)->count();
                            $records->where('closed', false)->each->update(['closed' => true]);

                            \Filament\Notifications\Notification::make()
                                ->title('Fatture chiuse')
                                ->body("{$count} fatture chiuse correttamente")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    Action::make('bulk_attach_to_model')
                        ->label('Associa Selezionati')
                        ->icon('heroicon-o-link')
                        ->color('success')
                        ->form([
                            Select::make('invoiceable_type')
                                ->label('Tipo')
                                ->options([
                                    'App\Models\Client' => 'Consulenti',
                                    'App\Models\Agent' => 'Agenti',
                                    // 'App\Models\Principal' => 'Mandanti',
                                ])
                                ->required()
                                ->reactive(),
                            Select::make('invoiceable_id')
                                ->label('Seleziona Record')
                                ->options(function (callable $get) {
                                    $type = $get('invoiceable_type');
                                    if (!$type)
                                        return [];

                                    return match ($type) {
                                        'App\Models\Client' => \App\Models\Client::pluck('name', 'id'),
                                        'App\Models\Agent' => \App\Models\Agent::pluck('name', 'id'),
                                        'App\Models\Principal' => \App\Models\Principal::pluck('name', 'id'),
                                        default => []
                                    };
                                })
                                ->required()
                                ->searchable()
                                ->getSearchResultsUsing(function (string $search, callable $get) {
                                    $type = $get('invoiceable_type');
                                    if (!$type)
                                        return [];

                                    return match ($type) {
                                        'App\Models\Client' => \App\Models\Client::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'),
                                        'App\Models\Agent' => \App\Models\Agent::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'),
                                        'App\Models\Principal' => \App\Models\Principal::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'),
                                        default => []
                                    };
                                }),
                        ])
                        ->action(function (array $data, $records) {
                            $totalUpdated = 0;
                            $invoiceableId = null;

                            // Se è selezionato "new", crea il record
                            if ($data['invoiceable_id'] === 'new') {
                                $newRecord = match ($data['invoiceable_type']) {
                                    'App\Models\Client' => \App\Models\Client::create(['name' => 'Nuovo Client ' . date('Y-m-d H:i')]),
                                    'App\Models\Agent' => \App\Models\Agent::create(['name' => 'Nuovo Agent ' . date('Y-m-d H:i')]),
                                    'App\Models\Principal' => \App\Models\Principal::create(['name' => 'Nuovo Principal ' . date('Y-m-d H:i')]),
                                    default => null
                                };

                                if ($newRecord) {
                                    $invoiceableId = $newRecord->id;
                                }
                            } else {
                                $invoiceableId = $data['invoiceable_id'];
                            }

                            if ($invoiceableId) {
                                foreach ($records as $record) {
                                    if (is_null($record->invoiceable_id)) {
                                        // Aggiorna il record corrente
                                        $record->update([
                                            'invoiceable_type' => $data['invoiceable_type'],
                                            'invoiceable_id' => $invoiceableId,
                                        ]);
                                        $totalUpdated++;

                                        // Associa tutte le altre fatture dello stesso supplier
                                        $additionalUpdated = \App\Models\PurchaseInvoice::where('supplier', $record->supplier)
                                            ->whereNull('invoiceable_id')
                                            ->where('id', '!=', $record->id)  // Escludi il record corrente
                                            ->update([
                                                'invoiceable_type' => $data['invoiceable_type'],
                                                'invoiceable_id' => $invoiceableId,
                                            ]);
                                        $totalUpdated += $additionalUpdated;
                                    }
                                }

                                $actionText = $data['invoiceable_id'] === 'new' ? 'creati e associati' : 'associati';
                                \Filament\Notifications\Notification::make()
                                    ->title('Fatture associate')
                                    ->body("{$totalUpdated} fatture {$actionText} correttamente (incluse tutte quelle degli stessi supplier)")
                                    ->success()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('document_date', 'desc');
    }
}
