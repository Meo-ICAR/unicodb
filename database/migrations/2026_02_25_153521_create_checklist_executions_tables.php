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
        // 1. TABELLA DELLE SOTTOMISSIONI (L'istanza della checklist)
        Schema::create('checklist_submissions', function (Blueprint $table) {
            $table->comment('Checklist execution instances attached to practices, audits, or other entities.');
            $table->id()->comment('ID univoco istanza checklist');

            // Corretto per usare char(36) come nelle altre tabelle
            $table->char('company_id', 36)->nullable()->comment('Agenzia proprietaria (multi-tenant)');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table
                ->foreignId('checklist_id')
                ->constrained('checklists')
                ->cascadeOnDelete()
                ->comment('Il template di partenza');

            // Relazione Polimorfica: permette di attaccare la checklist compilata a un Loan (Pratica), a un Audit, a una Company, ecc.
            $table->morphs('submittable');

            $table
                ->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment("L'operatore o ispettore che sta compilando");

            $table
                ->enum('status', ['draft', 'in_progress', 'completed'])
                ->default('draft')
                ->comment('Stato di avanzamento della compilazione');

            $table
                ->timestamp('completed_at')
                ->nullable()
                ->comment('Data di completamento definitivo');

            $table->timestamps();

            // Indici utili per le performance
            $table->index(['submittable_type', 'submittable_id'], 'submittable_index');
            $table->index('company_id');
        });

        // 2. TABELLA DELLE RISPOSTE (I dati inseriti dall'utente)
        Schema::create('checklist_answers', function (Blueprint $table) {
            $table->comment('Individual answers for checklist submissions with various response types.');
            $table->id()->comment('ID univoco risposta checklist');

            $table
                ->foreignId('checklist_submission_id')
                ->constrained('checklist_submissions')
                ->cascadeOnDelete()
                ->comment("Riferimento all'istanza di compilazione");

            $table
                ->foreignId('checklist_item_id')
                ->constrained('checklist_items')
                ->cascadeOnDelete()
                ->comment('Riferimento alla specifica domanda del template');

            // Campi per le varie tipologie di risposta
            $table
                ->text('value_text')
                ->nullable()
                ->comment('Risposta testuale libera o motivo di non trasparenza');

            $table
                ->boolean('value_boolean')
                ->nullable()
                ->comment('Risposta Vero/Falso per i toggle (es. Ha la targa OAM?)');

            $table
                ->json('value_array')
                ->nullable()
                ->comment('Array JSON per selezioni multiple (es. ID delle pratiche estratte a campione)');

            $table
                ->text('annotation')
                ->nullable()
                ->comment("Note interne o annotazioni dell'ispettore/operatore");

            // Campi opzionali per tracciare a quale modello è stato allegato fisicamente il file (ereditato dall'item_template)
            $table
                ->string('attached_model_type')
                ->nullable()
                ->comment('Tipo del modello a cui i file sono stati allegati (es. Principal, Agent)');

            $table
                ->unsignedBigInteger('attached_model_id')
                ->nullable()
                ->comment('ID del modello a cui i file sono stati allegati');

            $table->char('company_id', 36)->nullable()->comment('Agenzia proprietaria (multi-tenant)');

            $table->integer('ordine')->default(0)->comment('Ordine di visualizzazione');
            $table->tinyInteger('n_documents')->default(0)->comment('Numero di documenti richiesti (0=nessuno, 1=esatto, 99=multipli)');
            $table->index('ordine');
            $table->timestamps();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Assicuriamoci che per una singola "sottomissione" ci sia solo una "risposta" per "domanda"
            $table->unique(['checklist_submission_id', 'checklist_item_id'], 'unique_answer_per_submission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_answers');
        Schema::dropIfExists('checklist_submissions');
    }
};
