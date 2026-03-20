<?php

namespace Database\Seeders;

use App\Models\DocumentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentStatusSeederFixed extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Documento Assente',
                'status' => 'ASSENTE',
                'is_ok' => false,
                'is_rejected' => false,
                'description' => 'Il documento non è stato ancora caricato dal cliente',
            ],
            [
                'name' => 'Da Verificare',
                'status' => 'DA VERIFICARE',
                'is_ok' => false,
                'is_rejected' => false,
                'description' => 'Il documento è stato caricato e deve essere verificato',
            ],
            [
                'name' => 'In Verifica',
                'status' => 'IN VERIFICA',
                'is_ok' => false,
                'is_rejected' => false,
                'description' => 'Il documento è in fase di verifica da parte dello staff',
            ],
            [
                'name' => 'Documento Valido',
                'status' => 'OK',
                'is_ok' => true,
                'is_rejected' => false,
                'description' => 'Il documento è stato verificato e risulta valido',
            ],
            [
                'name' => 'Documento Difforme',
                'status' => 'DIFFORME',
                'is_ok' => false,
                'is_rejected' => true,
                'description' => 'Il documento presenta anomalie o non è conforme',
            ],
            [
                'name' => 'Documento scaduto',
                'status' => 'SCADUTO',
                'is_ok' => false,
                'is_rejected' => true,
                'description' => 'Il documento scaduto',
            ],
            [
                'name' => 'Informazioni Mancanti',
                'status' => 'RICHIESTA INFO',
                'is_ok' => false,
                'is_rejected' => false,
                'description' => 'Sono richieste informazioni aggiuntive al cliente',
            ],
            [
                'name' => 'Documento Errato',
                'status' => 'ERRATO',
                'is_ok' => false,
                'is_rejected' => true,
                'description' => 'Il documento caricato non è corretto',
            ],
            [
                'name' => 'Documento Annullato',
                'status' => 'ANNULLATO',
                'is_ok' => false,
                'is_rejected' => true,
                'description' => 'Il documento è stato annullato e deve essere ricaricato',
            ],
        ];

        foreach ($statuses as $status) {
            DocumentStatus::firstOrCreate(
                ['status' => $status['status']],
                $status
            );
        }
    }
}
