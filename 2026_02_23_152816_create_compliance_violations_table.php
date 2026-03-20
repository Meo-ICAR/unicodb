<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('compliance_violations', function (Blueprint $table) {
            $table->comment('Registro violazioni privacy');
            $table->id()->comment('ID univoco violazione');
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();
            $table->unique(['company_id', 'violation_type']);

            // Chi ha causato l'anomalia? (Può essere null se è un attacco esterno)
            $table
                ->foreignId('user_id')
                ->nullable()
                ->comment("ID dell'utente che ha causato la violazione")
                ->constrained('users')  // Indica esplicitamente la tabella users
                ->onDelete('set null');

            // A quale entità è legata? (Polimorfica: può essere un Dossier, un Client, ecc.)
            $table->nullableMorphs('violatable');

            // Dettagli della violazione
            $table->string('violation_type')->comment('Tipo violazione (es. accesso_non_autorizzato, kyc_scaduto)');
            $table->enum('severity', ['basso', 'medio', 'alto', 'critico'])->default('medio')->comment('Livello di gravità');
            $table->text('description')->comment("Descrizione dettagliata dell'evento");

            // Campi specifici per GDPR / Data Breach
            $table->integer('affected_subjects_count')->nullable()->comment('Numero approssimativo clienti/utenti coinvolti');
            $table->text('likely_consequences')->nullable()->comment('Possibili conseguenze per gli interessati');
            $table->dateTime('discovery_date')->nullable()->comment('Data e ora scoperta violazione (inizio 72h)');

            // Dati tecnici e legali (Fondamentali per il Garante Privacy)
            $table->ipAddress('ip_address')->nullable()->comment('Indirizzo IP sorgente');
            $table->text('user_agent')->nullable()->comment('Browser e dispositivo utilizzato');

            // Checkbox e date legali
            $table->boolean('is_dpa_notified')->default(false)->comment('Il Garante Privacy è stato notificato?');
            $table->dateTime('dpa_notified_at')->nullable()->comment('Data e ora notifica Garante');
            $table->text('dpa_not_notified_reason')->nullable()->comment('Se non notificato, motivazione legale');
            $table->boolean('are_subjects_notified')->default(false)->comment('I clienti coinvolti sono stati avvisati?');

            // Gestione e Risoluzione (L'Admin deve chiudere l'incidente)
            $table->timestamp('resolved_at')->nullable()->comment('Data e ora risoluzione violazione');
            $table
                ->foreignId('resolved_by')
                ->nullable()
                ->comment("ID dell'utente collegato")
                ->constrained('users')  // Indica esplicitamente la tabella users
                ->onDelete('set null');
            $table->text('resolution_notes')->nullable()->comment('Come è stata sanata la violazione?');

            $table->timestamps();
        });

        // Popolamento dati di esempio per compliance violations
        $this->seedComplianceViolations();
    }

    /**
     * Popola la tabella con dati di esempio
     */
    private function seedComplianceViolations(): void
    {
        $now = now();

        // Verifica se ci sono utenti nel sistema
        $userIds = DB::table('users')->pluck('id')->toArray();
        if (empty($userIds)) {
            // Se non ci sono utenti, non inseriamo dati di test
            return;
        }

        $firstUserId = $userIds[0];
        $secondUserId = $userIds[1] ?? $firstUserId;  // Usa il primo se non esiste il secondo
        $thirdUserId = $userIds[2] ?? $firstUserId;  // Usa il primo se non esiste il terzo

        $violations = [
            [
                'company_id' => null,  // Violazione globale
                'user_id' => null,
                'violatable_type' => null,
                'violatable_id' => null,
                'violation_type' => 'accesso_non_autorizzato',
                'severity' => 'alto',
                'description' => 'Tentativo di accesso a dati clienti da parte di utente non autorizzato. IP rilevato da rete esterna.',
                'affected_subjects_count' => 15,
                'likely_consequences' => 'Possibile esposizione dati personali e finanziari dei clienti coinvolti.',
                'discovery_date' => $now->subHours(3),
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'is_dpa_notified' => false,
                'dpa_notified_at' => null,
                'dpa_not_notified_reason' => 'Rischico valutato come improbabile per i diritti degli interessati',
                'are_subjects_notified' => false,
                'resolved_at' => null,
                'resolved_by' => null,
                'resolution_notes' => null,
                'created_at' => $now->subHours(3),
                'updated_at' => $now->subHours(3),
            ],
            [
                'company_id' => null,
                'user_id' => $firstUserId,
                'violatable_type' => 'App\Models\Client',
                'violatable_id' => 123,
                'violation_type' => 'kyc_scaduto',
                'severity' => 'medio',
                'description' => 'Documentazione KYC scaduta per cliente. Manca aggiornamento documenti identità.',
                'affected_subjects_count' => 1,
                'likely_consequences' => 'Impossibilità di proseguire con istruttoria finanziaria fino ad aggiornamento.',
                'discovery_date' => $now->subHours(6),
                'ip_address' => '10.0.0.15',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
                'is_dpa_notified' => false,
                'dpa_notified_at' => null,
                'dpa_not_notified_reason' => null,
                'are_subjects_notified' => true,
                'resolved_at' => $now->subHours(2),
                'resolved_by' => $firstUserId,
                'resolution_notes' => 'Cliente contattato e documentazione in via di aggiornamento.',
                'created_at' => $now->subHours(6),
                'updated_at' => $now->subHours(2),
            ],
            [
                'company_id' => null,
                'user_id' => null,
                'violatable_type' => null,
                'violatable_id' => null,
                'violation_type' => 'data_breach',
                'severity' => 'critico',
                'description' => 'Potenziale data breach rilevato da sistema di monitoraggio. Traffico anomalo su database clienti.',
                'affected_subjects_count' => 250,
                'likely_consequences' => 'Esposizione massiva dati personali con rischio di furto identità e frodi.',
                'discovery_date' => $now->subMinutes(30),
                'ip_address' => '185.220.101.182',
                'user_agent' => 'curl/7.68.0',
                'is_dpa_notified' => true,
                'dpa_notified_at' => $now->subMinutes(15),
                'dpa_not_notified_reason' => null,
                'are_subjects_notified' => false,
                'resolved_at' => null,
                'resolved_by' => null,
                'resolution_notes' => 'Indagine in corso. Sistema isolato e analisi forense avviata.',
                'created_at' => $now->subMinutes(30),
                'updated_at' => $now->subMinutes(30),
            ],
            [
                'company_id' => null,
                'user_id' => $thirdUserId,
                'violatable_type' => 'App\Models\Practice',
                'violatable_id' => 456,
                'violation_type' => 'forzatura_stato',
                'severity' => 'basso',
                'description' => 'Tentativo di modifica stato pratica da non autorizzato a completato.',
                'affected_subjects_count' => 0,
                'likely_consequences' => null,
                'discovery_date' => $now->subDay(),
                'ip_address' => '172.16.0.25',
                'user_agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36',
                'is_dpa_notified' => false,
                'dpa_notified_at' => null,
                'dpa_not_notified_reason' => null,
                'are_subjects_notified' => false,
                'resolved_at' => $now->subHours(12),
                'resolved_by' => $secondUserId,
                'resolution_notes' => "Accesso revocato all'utente. Pratica ripristinata stato corretto.",
                'created_at' => $now->subDay(),
                'updated_at' => $now->subHours(12),
            ],
        ];

        DB::table('compliance_violations')->insert($violations);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_violations');
    }
};
