<?php

namespace App\Filament\RelationManagers;

use App\Models\Checklist;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

abstract class BaseChecklistsRelationManager extends RelationManager
{
    protected static string $relationship = 'checklists';
    protected static ?string $title = 'Checklist Compliance e Procedure';

    /**
     * Override per personalizzare il titolo per specifici modelli
     */
    protected function getRelationManagerTitle(): string
    {
        return static::$title ?? 'Checklist Compliance e Procedure';
    }

    /**
     * Override per personalizzare il target type per specifici modelli
     */
    protected function getTargetTypeLabel(): string
    {
        $ownerClass = get_class($this->getOwnerRecord());
        return match ($ownerClass) {
            'App\Models\Agent' => 'Agente',
            'App\Models\Principal' => 'Mandante',
            'App\Models\Company' => 'Azienda',
            'App\Models\Client' => 'Cliente',
            'App\Models\Practice' => 'Pratica',
            default => class_basename($ownerClass),
        };
    }

    /**
     * Override per personalizzare la directory di upload
     */
    protected function getUploadDirectory(Checklist $record): string
    {
        $targetType = $this->getTargetTypeLabel();
        return 'checklist_docs/' . $targetType . '/' . $record->target_id;
    }

    /**
     * Override per personalizzare il messaggio di conferma
     */
    protected function getSuccessMessage(): string
    {
        $targetType = $this->getTargetTypeLabel();
        return "Checklist {$targetType} salvata con successo";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return $this->getOwnerRecord()->checklists();
            })
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Procedura')
                    ->weight('bold'),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'da_compilare' => 'danger',
                        'in_corso' => 'warning',
                        'completata' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Assegnata il')
                    ->date(),
            ])
            ->actions([
                // L'AZIONE MAGICA: Genera il form leggendo le domande!
                Action::make('compila')
                    ->label('Compila')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    // Modalità Wizard/Form Modale
                    ->modalHeading(fn(Checklist $record) => 'Compila: ' . $record->name)
                    ->modalWidth('2xl')
                    ->fillForm(function (Checklist $record): array {
                        // Pre-popoliamo il form con le risposte già date in passato
                        return $record->items->pluck('answer', 'item_code')->toArray();
                    })
                    ->form(function (Checklist $record) {
                        $schema = [];

                        // Leggiamo tutte le domande di questa specifica checklist
                        $items = $record->items()->orderBy('ordine')->get();

                        foreach ($items as $item) {
                            $component = null;

                            // 1. Capiamo che tipo di campo mostrare
                            if ($item->n_documents > 0) {
                                // È una richiesta di caricamento documenti
                                $component = SpatieMediaLibraryFileUpload::make($item->item_code)
                                    ->label($item->name)
                                    ->helperText($item->question)
                                    ->directory($this->getUploadDirectory($record))
                                    ->acceptedFileTypes(['application/pdf', 'image/*']);
                            } elseif (in_array($item->item_code, ['MOV_TIPO'])) {
                                // Esempio: Se sappiamo che è un menu a tendina
                                $component = Forms\Components\Select::make($item->item_code)
                                    ->label($item->name)
                                    ->helperText($item->question)
                                    ->options([
                                        'carico' => 'Carico (Nuovo Mandato)',
                                        'scarico' => 'Scarico (Cessazione)',
                                    ])
                                    ->live();  // FONDAMENTALE! Aggiorna il form in tempo reale quando cambia
                            } else {
                                // Domanda di testo generica
                                $component = Forms\Components\TextInput::make($item->item_code)
                                    ->label($item->name)
                                    ->helperText($item->question);
                            }

                            // 2. LA MAGIA DELLE DIPENDENZE (depends_on_code)
                            if ($item->depends_on_code) {
                                $component->visible(fn(Get $get) =>
                                    $get($item->depends_on_code) === $item->depends_on_value);
                            }

                            // 3. Obbligatorietà
                            if ($item->is_required) {
                                $component->required();
                            }

                            $schema[] = $component;
                        }

                        return $schema;
                    })
                    ->action(function (array $data, Checklist $record) {
                        // Salvataggio delle risposte
                        foreach ($data as $itemCode => $rispostaData) {
                            $record
                                ->items()
                                ->where('item_code', $itemCode)
                                ->update(['answer' => is_array($rispostaData) ? json_encode($rispostaData) : $rispostaData]);
                        }

                        // Se tutto è andato a buon fine, cambiamo lo stato della checklist
                        $record->update(['status' => 'completata']);

                        Forms\Components\Notification::make()
                            ->success()
                            ->title($this->getSuccessMessage())
                            ->send();
                    }),
            ]);
    }
}
