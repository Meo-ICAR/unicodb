<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('function_privacys', function (Blueprint $table) {
            $table->comment('Schede Privacy per funzioni aziendali');
            $table->id()->comment('ID univoco scheda privacy funzione');

            // Collegamento al reparto/funzione aziendale che effettua il trattamento
            $table
                ->foreignId('business_function_id')
                ->constrained('business_functions')
                ->onDelete('cascade')
                ->comment('ID funzione business di riferimento');  // Se elimino il reparto, elimino le sue schede privacy

            // Nome dell'attività di trattamento (es. "Gestione Pratiche Mutuo")
            $table->string('processing_activity')->comment('Descrizione attività trattamento dati');

            // Categorie di Interessati (es. "Clienti", "Dipendenti", "Collaboratori")
            $table->string('data_subjects')->comment('Categorie soggetti interessati');

            // Categorie di Dati Personali trattati (es. "Dati anagrafici, reddituali, sanitari")
            $table->text('data_categories')->comment('Categorie dati personali trattati');

            // Finalità del trattamento
            $table->text('purpose')->comment('Finalità trattamento dati personali');

            // Base Giuridica (Art. 6 GDPR)
            $table->enum('legal_basis', [
                'Consenso',
                'Esecuzione di un contratto',
                'Obbligo di legge',
                'Legittimo interesse',
                'Interesse vitale',
                'Interesse pubblico'
            ])->comment('Base giuridica trattamento dati (Art. 6 GDPR)');

            // Categorie di Destinatari (a chi vengono inviati i dati? es. Banche, OAM)
            $table->string('recipients')->nullable()->comment('Categorie destinatari dati');

            // Trasferimento extra-UE (Sì/No o dettagli)
            $table->string('non_eu_transfer')->default('Nessuno')->comment('Trasferimento dati extra-UE');

            // Tempi di conservazione (Data Retention)
            $table->string('retention_period')->comment('Periodo conservazione dati');

            // Misure di sicurezza generali applicate (es. "Crittografia, Pseudonimizzazione")
            $table->text('security_measures')->nullable()->comment('Misure sicurezza applicate');

            // Stato della scheda (per tenere lo storico)
            $table->boolean('is_active')->default(true)->comment('Se scheda privacy è attiva');

            // Data di creazione
            $table->timestamp('start_at')->nullable()->comment('Data di inizio validità');
            // Data di ultima modifica
            $table->timestamp('end_at')->nullable()->comment('Data di fine validità');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('function_privacys');
    }
};
