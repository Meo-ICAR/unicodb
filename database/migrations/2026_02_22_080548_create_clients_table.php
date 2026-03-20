<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->comment('Clienti (Richiedenti credito) associati in modo esclusivo a una specifica agenzia (Tenant).');
            $table->increments('id')->comment('ID intero autoincrementante');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            $table->boolean('is_person')->default(true)->comment('Persona fisica (true) o giuridica (false)');
            $table->string('name')->comment('Cognome (se persona fisica) o Ragione Sociale (se giuridica)');
            $table->string('first_name')->nullable()->comment('Nome persona fisica');
            $table->string('tax_code', 16)->nullable()->comment('Codice Fiscale o Partita IVA del cliente');
            $table->string('email')->nullable()->comment('Email di contatto principale');
            $table->string('phone', 50)->nullable()->comment('Recapito telefonico');
            $table->boolean('is_pep')->default(false)->comment('Persona Politicamente Esposta');
            $table->unsignedInteger('client_type_id')->nullable()->comment('Classificazione cliente');
            $table->boolean('is_sanctioned')->default(false)->comment('Presente in liste antiterrorismo/blacklists');

            $table->boolean('is_remote_interaction')->default(false)->comment('Operatività a distanza = Rischio più alto');
            // Consensi Obbligatori
            $table->timestamp('general_consent_at')->nullable()->comment('Consenso generale al trattamento base');
            $table->timestamp('privacy_policy_read_at')->nullable()->comment('Data presa visione informativa Art.13');
            $table->timestamp('consent_special_categories_at')->nullable()->comment('Consenso dati sanitari/giudiziari per polizze/CQS');
            $table->timestamp('consent_sic_at')->nullable()->comment('Consenso interrogazione CRIF/CTC/Experian');

            // Consensi Facoltativi
            $table->timestamp('consent_marketing_at')->nullable()->comment('Consenso comunicazioni commerciali e newsletter');

            // (Opzionale ma consigliato per il futuro) Consenso per la profilazione
            $table->timestamp('consent_profiling_at')->nullable()->comment('Consenso profilazione abitudini di consumo/spesa');

            // Stati del Workflow (Spatie Model States o Enum)
            $table->string('status')->default('raccolta_dati')->comment('raccolta_dati, valutazione_aml, approvata, sos_inviata, chiusa')->nullable();

            $table->string('contoCOGE')->nullable()->comment('Conto COGE');

            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data acquisizione cliente');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Ultima modifica anagrafica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
