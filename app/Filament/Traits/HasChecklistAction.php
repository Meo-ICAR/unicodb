<?php

namespace App\Filament\Traits;

use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Spatie\MediaLibrary\HasMedia;

trait HasChecklistAction
{
    public static function getChecklistActions(
        ?string $code = null,
        string $label = 'Checklist',
        string $icon = 'heroicon-o-clipboard-document-check'
    ): array {
        $codeName = $code ?? 'generale';

        // --- 1. AZIONE: GENERA (Appare solo se NON esiste) ---
        $actionGenera = Action::make("genera_{$codeName}")
            ->label(" {$label}")
            ->icon('heroicon-o-sparkles')
            ->color('success')
            ->visible(fn($record) => !DB::table('checklists')
                ->where('target_id', $record->id)
                ->where('target_type', get_class($record))
                ->where('code', $code)
                ->exists())
            ->requiresConfirmation()
            ->modalHeading("Genera Nuova {$label}")
            ->modalDescription('Non è presente una checklist per questo record. Vuoi crearla ora partendo dal template?')
            ->action(function ($record) use ($code, $label) {
                // Cerchiamo il template (dove target_id è null)
                $template = DB::table('checklists')
                    ->where('code', $code)
                    ->whereNull('target_id')
                    ->first();

                if (!$template) {
                    Notification::make()->title("Errore: Template '{$code}' non trovato.")->danger()->send();
                    return;
                }

                // Inseriamo la testata
                $newId = DB::table('checklists')->insertGetId([
                    'target_id' => $record->id,
                    'target_type' => get_class($record),
                    'code' => $code,
                    'name' => $template->name ?? $label,
                    'company_id' => $record->company_id ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Copiamo gli items dal template
                $templateItems = DB::table('checklist_items')->where('checklist_id', $template->id)->get();
                foreach ($templateItems as $item) {
                    DB::table('checklist_items')->insert([
                        'checklist_id' => $newId,
                        'name' => $item->name,
                        'description' => $item->description,
                        'question' => $item->question,
                        'url_step' => $item->url_step,
                        'document_type_code' => $item->document_type_code,
                        'is_completed' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                Notification::make()->title('Checklist generata correttamente')->success()->send();
            });

        // --- 2. AZIONE: GESTISCI (Appare solo se ESISTE) ---
        $actionGestisci = Action::make("gestisci_{$codeName}")
            ->label($label)
            ->icon('heroicon-o-clipboard-document-check')
            ->slideOver()
            ->visible(fn($record) => DB::table('checklists')->where('target_id', $record->id)->where('code', $code)->exists())
            ->fillForm(function ($record) use ($code): array {
                // USIAMO DB PURO PER EVITARE IL LOOP DEI MODELLI
                $checklist = DB::table('checklists')
                    ->where('target_id', $record->id)
                    ->where('target_type', get_class($record))
                    ->where('code', $code)
                    ->first();

                if (!$checklist)
                    return [];

                $items = DB::table('checklist_items')
                    ->where('checklist_id', $checklist->id)
                    ->get(['id', 'question', 'description', 'url_step', 'answer'])
                    ->map(fn($item) => (array) $item)  // Convertiamo oggetti stdClass in array
                    ->toArray();

                return [
                    'items' => $items,
                ];
            })
            ->form([
                Repeater::make('items')
                    ->label('Passaggi')
                    ->schema([
                        TextInput::make('id')->hidden(),
                        TextInput::make('question')
                            ->label('Domanda')
                            ->disabled()
                            ->columnSpan(3),
                        Placeholder::make('link')
                            ->label('Link')
                            ->content(fn($get) => $get('url_step')
                                ? new HtmlString("<a href='{$get('url_step')}' target='_blank' style='color:blue; font-weight:bold;'>Apri</a>")
                                : '-')
                            ->columnSpan(1),
                        Textarea::make('description')
                            ->label('Istruzioni')
                            ->disabled()
                            ->rows(1)
                            ->columnSpan(4),
                        Textarea::make('answer')
                            ->label('Tua Risposta')
                            ->rows(2)
                            ->columnSpan(4),
                    ])
                    ->columns(4)
                    ->addable(false)
                    ->deletable(false)
            ])
            ->action(function ($record, array $data) {
                // Salvataggio tramite DB puro
                foreach ($data['items'] as $itemData) {
                    DB::table('checklist_items')
                        ->where('id', $itemData['id'])
                        ->update([
                            'answer' => $itemData['answer'] ?? null,
                            'is_completed' => !empty($itemData['answer']),
                            'updated_at' => now(),
                        ]);
                }

                Notification::make()->title('Dati salvati con successo')->success()->send();
            });

        return [$actionGenera, $actionGestisci];
    }
}
