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
        Schema::create('aui_records', function (Blueprint $table) {
            $table->comment('Archivio Unico Informatico (AUI) records for Bank of Italy reporting.');
            $table->id()->comment('ID univoco record AUI');
            // Colleghiamo il record definitivo al log che lo ha generato
            $table->foreignId('activity_log_id')->nullable()->constrained('activity_log')->nullOnDelete();
            $table->unsignedInteger('practice_id')->comment('Riferimento alla pratica')->nullable();
            $table->unsignedInteger('client_id')->comment('Riferimento al cliente')->nullable();
            // I dati intoccabili per Banca d'Italia
            $table->string('codice_univoco_aui')->unique()->comment("Codice univoco AUI per Banca d'Italia");  // Es: AUI-2026-0001
            $table->string('tipo_registrazione')->comment('Tipo di registrazione AUI');
            $table->date('data_registrazione')->comment('Data di registrazione AUI');
            $table->decimal('importo_operazione', 15, 2)->comment("Importo dell'operazione finanziaria");
            $table->string('profilo_rischio')->default('basso')->comment('Profilo di rischio cliente');

            $table->boolean('is_annullato')->default(false)->comment('Se il record è stato annullato');
            $table->string('motivo_annullamento')->nullable()->comment("Motivo dell'annullamento");
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade

            $table->timestamps();
            // NESSUN SOFT DELETE: I record AUI non si cancellano MAI fisicamente.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auis');
    }
};
