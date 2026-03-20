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
        Schema::create('document_status', function (Blueprint $table) {
            $table->comment('Tabella stati dei documenti');
            $table->id()->comment('ID univoco dello stato');
            $table->string('name')->comment('Nome dello stato (es. ASSENTE, DA VERIFICARE, etc.)');
            $table->enum('status', [
                'ASSENTE',
                'DA VERIFICARE',
                'IN VERIFICA',
                'OK',
                'DIFFORME',
                'RICHIESTA INFO',
                'ERRATO',
                'ANNULLATO'
            ])->unique()->comment('Stato del documento');
            $table->boolean('is_ok')->default(false)->comment('True se il documento è valido e accettato');
            $table->boolean('is_rejected')->default(false)->comment('True se il documento è stato respinto');
            $table->text('description')->nullable()->comment('Descrizione dettagliata dello stato');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_status');
    }
};
