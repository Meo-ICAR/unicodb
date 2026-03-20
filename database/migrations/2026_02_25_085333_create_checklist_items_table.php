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
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->comment('Elementi delle checklist con domande e allegati');
            $table->id()->comment('ID univoco elemento checklist');
            $table->unsignedBigInteger('checklist_id')->comment('Checklist di appartenenza');

            $table->string('ordine')->comment('Ordine della domanda/elemento')->nullable();
            $table->string('phase')->comment('Fase della checklist')->nullable();
            $table->boolean('is_phaseclose')->default(false)->comment('Se attività di chiusura della fase');
            $table->string('name')->comment('Nome della domanda/elemento')->nullable();
            $table->string('item_code')->nullable()->comment('Codice univoco della domanda');
            $table->text('question')->nullable()->comment('Testo della domanda');
            $table->text('answer')->nullable()->comment("Risposta data dall'utente");
            $table->text('description')->nullable()->comment('Descrizione o note aggiuntive');
            $table->text('descriptioncheck')->nullable()->comment('Descrizione verifica conformita da effettuare');
            $table->text('annotation')->nullable()->comment('Annotazioni interne');
            $table->boolean('is_required')->default(false)->comment('Se obbligatorio');
            $table->enum('attach_model', ['principal', 'agent', 'company', 'audit'])->nullable()->comment('Modello a cui allegare documento');
            $table->string('attach_model_id')->nullable()->comment('ID del modello per allegato');
            $table->integer('n_documents')->default(false)->comment('Numero documenti da allegare 0= no, 99=multi');
            $table->string('repeatable_code')->nullable()->comment('Codice se ripetibile (es. documenti annuali)');
            $table->string('document_type_codegroup')->nullable()->comment('Codice gruppo documenti');
            $table->string('document_type_code')->nullable()->comment('Codice gruppo documenti');

            // Logica condizionale

            $table->string('depends_on_code')->nullable()->comment('Il codice della domanda da cui dipende');
            $table->string('depends_on_value')->nullable()->comment('Il valore che deve avere per attivarsi');
            $table->enum('dependency_type', ['show_if', 'hide_if'])->nullable()->comment('Attiva / Disattiva condizionale');
            $table
                ->string('url_step')
                ->nullable()
                ->comment('Link esterno per step procedure');
            $table
                ->string('url_callback')
                ->nullable()
                ->comment('Link esterno per callback');

            $table->timestamps();

            // Indici
            $table->index('checklist_id');
            $table->index(['attach_model', 'attach_model_id']);

            // Foreign keys
            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_items');
    }
};
