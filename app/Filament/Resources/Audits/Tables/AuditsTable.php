<?php

namespace App\Filament\Resources\Audits\Tables;

use App\Models\Checklist;
use App\Models\ChecklistAnswer;
use App\Models\ChecklistSubmission;
use App\Services\ChecklistService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->with(['auditable', 'requester']))
            ->columns([
                TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('auditable_type')
                    ->label('Tipo Oggetto')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\Models\Company' => 'Azienda',
                        'App\Models\Agent' => 'Agente',
                        'App\Models\Employee' => 'Dipendente',
                        'App\Models\Client' => 'Cliente',
                        'App\Models\Principal' => 'Mandante',
                        default => $state,
                    })
                    ->badge()
                    ->sortable(),
                TextColumn::make('auditable.name')
                    ->label('Oggetto Audit')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('requester_type')
                    ->label('Tipo Richiedente')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'App\Models\Principal' => 'Mandante',
                        'App\Models\Agent' => 'Agente',
                        'App\Models\RegulatoryBody' => 'Ente Regolatore',
                        'App\Models\Company' => 'Azienda',
                        'App\Models\Employee' => 'Dipendente',
                        default => $state,
                    })
                    ->badge()
                    ->sortable(),
                TextColumn::make('requester.name')
                    ->label('Richiedente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('principal.name')
                    ->label('Mandante (Legacy)')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('agent.name')
                    ->label('Agente (Legacy)')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('regulatoryBody.name')
                    ->label('Ente Regolatore')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('client.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('emails')
                    ->label('Email Notifiche')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference_period')
                    ->label('Periodo Riferimento')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')
                    ->label('Data Inizio')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Data Fine')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Stato')
                    ->formatStateUsing(fn($state) => match ($state) {
                        'PROGRAMMATO' => 'Programmato',
                        'IN_CORSO' => 'In Corso',
                        'COMPLETATO' => 'Completato',
                        'ARCHIVIATO' => 'Archiviato',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'PROGRAMMATO' => 'info',
                        'IN_CORSO' => 'warning',
                        'COMPLETATO' => 'success',
                        'ARCHIVIATO' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('overall_score')
                    ->label('Valutazione')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                // Da inserire nel metodo getActions() o getHeaderActions() della tua pagina Filament
                Action::make('compila_checklist')
                    ->label('Compila Checklist')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('primary')
                    ->slideOver()  // Apre un comodo pannello laterale invece di una modale
                    ->fillForm(function (Model $record) {
                        // $record è il modello corrente (es. la Pratica o l'Audit)

                        // 1. Troviamo il template di checklist corretto (es. Cessione del Quinto)
                        $template = Checklist::where('type', 'loan_management')->first();
                        if (!$template) {
                            return [];
                        }

                        // 2. Cerchiamo se c'è già una sottomissione in corso per questa pratica
                        $submission = ChecklistSubmission::firstOrCreate([
                            'checklist_id' => $template->id,
                            'submittable_type' => get_class($record),
                            'submittable_id' => $record->id,
                        ], [
                            'status' => 'in_progress',
                            'user_id' => auth()->id(),
                        ]);

                        // 3. Carichiamo i dati pregressi nel formato richiesto dal form (item_CODICE_bool, ecc.)
                        $formData = [];
                        $answers = $submission->answers()->with('item')->get();

                        foreach ($answers as $answer) {
                            $code = $answer->item->item_code;
                            $formData["item_{$code}_bool"] = $answer->value_boolean;
                            $formData["item_{$code}_text"] = $answer->value_text;
                            $formData["item_{$code}_files"] = $answer->value_array;  // FileUpload gestisce gli array di percorsi
                        }

                        return $formData;
                    })
                    ->form(function () {
                        $template = Checklist::where('type', 'loan_management')->first();
                        if (!$template) {
                            return [];
                        }
                        return ChecklistService::getFormSchema($template);
                    })
                    ->action(function (array $data, Model $record) {
                        // QUESTO VIENE ESEGUITO QUANDO L'UTENTE CLICCA "SALVA"

                        $template = Checklist::where('type', 'loan_management')->first();
                        if (!$template) {
                            return;
                        }

                        $submission = ChecklistSubmission::where('submittable_type', get_class($record))
                            ->where('submittable_id', $record->id)
                            ->where('checklist_id', $template->id)
                            ->first();

                        // Estraiamo tutti gli item del template
                        $items = $template->checklistItems()->get();

                        foreach ($items as $item) {
                            $code = $item->item_code;

                            // Prepariamo i valori recuperati dallo stato del form
                            $valBool = $data["item_{$code}_bool"] ?? null;
                            $valText = $data["item_{$code}_text"] ?? null;
                            $valFiles = $data["item_{$code}_files"] ?? null;

                            // Se è un file singolo, FileUpload potrebbe restituire una stringa invece di un array, normalizziamo
                            if (is_string($valFiles)) {
                                $valFiles = [$valFiles];
                            }

                            // Salviamo (o aggiorniamo) la singola risposta nel database
                            ChecklistAnswer::updateOrCreate(
                                [
                                    'checklist_submission_id' => $submission->id,
                                    'checklist_item_id' => $item->id,
                                ],
                                [
                                    'value_boolean' => $valBool,
                                    'value_text' => $valText,
                                    'value_array' => $valFiles,  // Array JSON con i percorsi dei file
                                    'attached_model_type' => $item->attach_model ? $item->attach_model : null,
                                    // Se l'attach_model è 'principal', e la tua Pratica ha $record->principal_id, lo colleghi
                                    'attached_model_id' => ($item->attach_model === 'principal') ? $record->principal_id : null,
                                ]
                            );
                        }
                    }),
                // Opzionale: Notifica di successo
                // Notification::make()->title('Checklist salvata con successo')->success()->send();
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
