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
        Schema::create('audits', function (Blueprint $table) {
            $table->comment('Sessioni di Audit richieste da OAM, Mandanti o effettuate internamente.');
            $table->increments('id')->comment('ID univoco audit');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade

            // Campi polimorfici per il richiedente
            $table->string('requester_type')->nullable()->comment('Tipo del modello requester (principal, agent, regulatory_body, company)');
            $table->string('requester_id')->nullable()->comment('ID del modello requester (supporta UUID e integer)');

            // Campi polimorfici per associare l'audit a diversi modelli
            $table->string('auditable_type')->nullable()->comment('Tipo del modello auditabile (agent, employee, company_branch, principal, company)');
            $table->string('auditable_id')->nullable()->comment('ID del modello auditabile (supporta UUID e integer)');

            $table->string('title')->comment("Titolo dell'ispezione (es. Audit Semestrale Trasparenza 2026)")->nullable();
            $table->string('emails')->default('')->comment('Lista email per notifiche esiti audit')->nullable();
            $table->string('reference_period', 100)->nullable()->comment('Periodo oggetto di analisi (es. Q1-Q2 2025)');
            $table->date('start_date')->comment('Data inizio ispezione')->nullable();
            $table->date('end_date')->nullable()->comment('Data chiusura ispezione');
            $table->enum('status', ['PROGRAMMATO', 'IN_CORSO', 'COMPLETATO', 'ARCHIVIATO'])->nullable()->default('PROGRAMMATO')->comment('Stato di avanzamento audit');
            $table->string('overall_score', 50)->nullable()->comment('Valutazione sintetica finale (es. Conforme, Conforme con rilievi, Non Conforme)');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
