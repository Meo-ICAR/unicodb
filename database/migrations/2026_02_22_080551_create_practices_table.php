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
        Schema::create('practices', function (Blueprint $table) {
            $table->comment('Pratiche di mediazione (Mutui, Cessioni, Prestiti personali) caricate a sistema.');
            $table->increments('id')->comment('ID intero autoincrementante della pratica');

            // Aggiungiamo il collegamento al mandato subito dopo l'id
            $table->foreignId('client_mandate_id')->nullable()->constrained('client_mandates')->cascadeOnDelete();

            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            $table->unsignedInteger('principal_id')->nullable()->comment('Mandante (banca)');
            $table->foreign('principal_id')->references('id')->on('principals')->onDelete('set null');

            $table->unsignedInteger('agent_id')->nullable()->comment('Agente o collaboratore per provvigioni');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');

            $table->unsignedInteger('employee_id')->nullable()->comment('Dipendente che segue la pratica');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('set null');

            $table->string('name')->nullable()->comment('Codice o nome identificativo (es. Mutuo Acquisto Prima Casa Rossi)');

            $table->string('CRM_code')->nullable()->comment('Codice CRM interno');
            $table->string('abi')->nullable()->comment('Abi banca erogatrice');
            $table->string('bank_name')->nullable()->comment('Nome banca erogatrice');
            $table->string('tipo_prodotto')->nullable()->comment('tipo prodotto CRM');
            $table->string('principal_code')->nullable()->comment('Codice mandante');
            $table->decimal('amount', 12)->nullable()->comment('Importo del finanziamento/mutuo richiesto o erogato');
            $table->decimal('net', 12)->nullable()->comment('Netto erogato');
            $table->decimal('brokerage_fee', 10, 2)->nullable()->comment('Provvigione pattuita');
            $table->decimal('principal_fee', 10, 2)->nullable()->comment('Provvigione mandante');
            $table->decimal('client_fee', 10, 2)->nullable()->comment('Provvigione cliente');
            $table->decimal('prize_fee', 10, 2)->nullable()->comment('Provvigione assicurativa');
            $table->decimal('insurance_fee', 10, 2)->nullable()->comment('Provvigione assicurazione');
            $table->unsignedInteger('practice_scope_id')->nullable()->comment('Ambito della pratica');
            // Lo stato governato da Spatie Model States
            $table->string('status', 50)->nullable()->default('working')->comment('Stato interno attuale: working, rejected, perfected');
            $table->string('statoproforma', 50)->nullable()->comment('Stato proforma: Inserito / Sospeso / Annullato / Inviato / Abbinato');
            // --- DATE OPERATIVE E CRM ---
            $table->string('oam_principal')->nullable()->comment('Differenzia istituti non convenzionati');
            // --- DATE COMPLIANCE: BUSINESS, ENASARCO, AUI ---
            $table->date('inserted_at')->nullable()->comment('Data inserimento pratica');  // Quando entra nel CRM
            $table->date('approved_at')->nullable()->comment('Data approvazione pratica');  // Quando viene approvata
            // --- DATE COMPLIANCE: BUSINESS, ENASARCO, AUI ---
            $table->date('erogated_at')->nullable()->comment('Data erogazione finanziamento / stipula mutuo notaio');  // Innesca Enasarco, AUI operazione e Statistica OAM -Alimenta l'AUI di Esecuzione Operazione - in genere Alimenta l'AUI di Chiusura Rapporto ma non so se la fatturazione a cliente e' successiva cosa accade
            $table->date('rejected_at')->nullable()->comment('Data rifiuto pratica');  // Alimenta l'AUI di Chiusura Rapporto
            $table->string('rejected_reason')->nullable()->comment('Causale rifiuto pratica es. Rifiutata banca');

            // --- DATE AMMINISTRATIVE ---
            $table->date('status_at')->nullable()->comment('Data stato perfezionata ovvero possibile emissione proforma ad agente');

            // data fattura a banca Fa reddito per Quota Variabile OAM
            // data fattura a cliente Fa reddito per Quota Variabile OAM

            $table->text('description')->nullable()->comment('Descrizione pratica');
            $table->text('annotation')->nullable()->comment('Annotazioni interne sulla pratica');

            $table->date('perfected_at')->nullable()->comment('Data perfezionamento pratica');

            $table->boolean('is_active')->default(true)->comment('Pratica attiva/inattiva');

            $table->boolean('is_convenctioned')->default(true)->comment('Pratica convenzionata');

            $table->boolean('is_notowned')->default(false)->comment('Pratica non di proprietà');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practices');
    }
};
