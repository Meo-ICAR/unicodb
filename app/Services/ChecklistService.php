<?php

namespace App\Services;

use App\Models\Checklist;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChecklistService
{
    /**
     * Genera lo schema Filament dinamico partendo da un template Checklist
     */
    public static function getFormSchema(Checklist $checklist): array
    {
        $schema = [];

        // Recuperiamo le domande ordinate
        $items = $checklist->items()->orderBy('ordine')->get();

        foreach ($items as $item) {
            // Chiavi univoche per il form state di Filament
            $boolKey = "item_{$item->item_code}_bool";
            $textKey = "item_{$item->item_code}_text";
            $filesKey = "item_{$item->item_code}_files";

            // Costruiamo il wrapper della singola domanda
            $fieldGroup = Section::make($item->name)
                ->description($item->question . ($item->description ? ' - ' . $item->description : ''))
                ->schema(function () use ($item, $boolKey, $textKey, $filesKey) {
                    $fields = [];

                    // SE NON CI SONO ALLEGATI (n_documents == 0) -> È una domanda testuale o Vero/Falso
                    if ($item->n_documents == 0) {
                        $fields[] = Grid::make(2)->schema([
                            Toggle::make($boolKey)
                                ->label('Risposta (Sì/No)')
                                ->inline(false)
                                ->live(),  // Reattivo per la logica condizionale
                            Textarea::make($textKey)
                                ->label('Testo della risposta / Note aggiuntive')
                                ->rows(2)
                                ->live(onBlur: true),
                        ]);
                    }
                    // SE CI SONO ALLEGATI (n_documents > 0)
                    else {
                        $fields[] = FileUpload::make($filesKey)
                            ->label('Carica Allegati')
                            ->directory("checklist_files/{$item->attach_model}/{$item->item_code}")
                            ->multiple($item->n_documents > 1)  // Se 99, è multiplo
                            ->maxFiles($item->n_documents > 1 ? null : 1)
                            ->preserveFilenames()
                            ->reorderable()
                            ->appendFiles()
                            ->required($item->is_required);

                        // Anche con i file, lasciamo un campo note opzionale
                        $fields[] = Textarea::make($textKey)
                            ->label('Annotazioni (Opzionale)')
                            ->rows(1);
                    }

                    return $fields;
                })
                ->collapsible()
                ->compact();  // Design più pulito

            // --- APPLICAZIONE LOGICA CONDIZIONALE ---
            if ($item->depends_on_code && $item->dependency_type) {
                // La dipendenza si basa quasi sempre sulla risposta "Vero/Falso" (il toggle)
                $parentBoolKey = "item_{$item->depends_on_code}_bool";

                $fieldGroup->visible(function (Get $get) use ($item, $parentBoolKey) {
                    $parentValue = $get($parentBoolKey);

                    // Normalizza il valore atteso (1 = true, 0 = false)
                    $expectedValue = in_array($item->depends_on_value, ['1', 'true', 'si', 'vero']) ? true : false;

                    if ($item->dependency_type === 'show_if') {
                        return $parentValue === $expectedValue;
                    }

                    if ($item->dependency_type === 'hide_if') {
                        return $parentValue !== $expectedValue;
                    }

                    return true;
                });
            }

            $schema[] = $fieldGroup;
        }

        return $schema;
    }

    /**
     * Clona un template di checklist e lo assegna a un'entità (Agente, Pratica, ecc.)
     *
     * @param Model $target Il modello a cui assegnare la checklist (es. istanza di Agent o Pratica)
     * @param string $templateCode Il codice univoco del template OAM/Audit da clonare
     * @return Checklist La nuova checklist appena generata
     * @throws Exception Se il template non esiste o c'è un errore nel database
     */
    public function assignTemplate(Model $target, string $templateCode): Checklist
    {
        // Usiamo una Transaction: se fallisce la copia delle domande, non salviamo nemmeno la testata. Tutto o niente.
        return DB::transaction(function () use ($target, $templateCode) {
            // 1. Troviamo il Template originale (quello intoccabile)
            $template = Checklist::where('code', $templateCode)
                ->where('is_template', true)
                ->firstOrFail();

            // 2. VALIDAZIONE: Se il template è unico, verifichiamo che non esista già per questo target
            if ($template->is_unique) {
                $existingChecklist = Checklist::where('target_type', get_class($target))
                    ->where('target_id', $target->id)
                    ->where('name', $template->name)
                    ->where('is_unique', true)
                    ->first();

                if ($existingChecklist) {
                    throw new \Exception("La checklist '{$template->name}' è già stata assegnata a questo target e non può essere duplicata.");
                }
            }

            // 3. Fotocopiamo la testata
            $nuovaChecklist = $template->replicate();
            $nuovaChecklist->is_template = false;  // Questa è un'istanza operativa

            // Magia del polimorfismo: capisce da solo se $target è un Agent, Pratica, ecc.
            $nuovaChecklist->target_type = get_class($target);
            $nuovaChecklist->target_id = $target->id;

            $nuovaChecklist->status = 'da_compilare';
            $nuovaChecklist->save();

            // 4. Fotocopiamo tutte le domande (Items)
            foreach ($template->items as $item) {
                $nuovoItem = $item->replicate();
                $nuovoItem->checklist_id = $nuovaChecklist->id;

                // Pulizia preventiva: ci assicuriamo che parta intonsa
                $nuovoItem->answer = null;

                // Se la domanda prevedeva un allegato sul modello (es. attach_model = 'agent'), leghiamo l'ID
                if ($nuovoItem->attach_model) {
                    $nuovoItem->attach_model_id = $target->id;
                }

                $nuovoItem->save();
            }

            return $nuovaChecklist;
        });
    }
}
