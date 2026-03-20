<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OamChecklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Creiamo o aggiorniamo la Checklist principale
        DB::table('checklists')->updateOrInsert(
            ['slug' => 'oam-fascicolo'],
            [
                'name' => 'Fascicolo OAM (Controllo Vigilanza)',
                'description' => 'Checklist completa per ispezioni e controlli OAM come da richieste standard.',
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // Recuperiamo l'ID della checklist appena inserita
        $checklist = DB::table('checklists')->where('slug', 'oam-fascicolo')->first();

        // 2. Elenco degli slug dei documenti necessari (mix tra vecchi e nuovi)
        $requiredDocumentSlugs = [
            'carta-identita',  // Documentazione anagrafica
            'codice-fiscale',  // Documentazione anagrafica
            'privacy-informativa',  // Modulo privacy
            'trasparenza-web',  // Foglio informativo sul servizio
            'attestazione-ricevimento-informativa',  // NUOVO: Attestazione ricevimento
            'questionario-aml',  // Questionario adeguata verifica (Antiriciclaggio)
            'incarico-mediazione',  // Contratto/incarico mediazione
            'fattura-pratica-mediazione',  // NUOVO: Fatture pratica
            'comunicazione-compenso-istituto',  // NUOVO: Comunicazione compenso istituto
            'ricevuta-comunicazione-compenso',  // NUOVO: Attestazione ricezione compenso
            'documentazione-servizi-congiunti',  // NUOVO: Servizi offerti congiuntamente
            'modulo-secci',  // Modulo IEBCC
            'modulo-pies'  // NUOVO: Modulo PIES (in alternativa o insieme al SECCI)
        ];

        // Recuperiamo gli ID dei documenti in base agli slug
        $documentTypes = DB::table('document_types')
            ->whereIn('slug', $requiredDocumentSlugs)
            ->get();

        // 3. Inseriamo le relazioni nella tabella pivot
        // NOTA: Cambia 'checklist_document_type' se la tua tabella pivot ha un nome diverso
        // (es. 'checklist_items' o 'document_type_checklist')
        $pivotTableName = 'checklist_document_type';

        foreach ($documentTypes as $index => $document) {
            DB::table($pivotTableName)->updateOrInsert(
                [
                    'checklist_id' => $checklist->id,
                    'document_type_id' => $document->id
                ],
                [
                    // Se hai un campo per l'ordinamento (es. 'order' o 'position')
                    // 'position' => $index + 1,
                    'is_required' => 1,  // Assumendo che ci sia un flag per l'obbligatorietà
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $this->command->info("Checklist 'OAM-fascicolo' creata con successo con " . $documentTypes->count() . ' documenti collegati.');
    }
}
