<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_mandates', function (Blueprint $table) {
            $table->comment('Client mandates for financial services and AUI reporting.');
            $table->id()->comment('ID univoco mandato cliente');
            $table->unsignedInteger('client_id')->comment('Riferimento al cliente coinvolto');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            // Dati del Mandato (Utili per AUI Instaurazione)
            $table->string('numero_mandato')->unique()->comment('Numero identificativo mandato');
            $table->date('data_firma_mandato')->comment('Innesca Instaurazione Rapporto AUI');
            $table->date('data_scadenza_mandato')->comment('Innesca Chiusura Rapporto AUI (se non erogato prima)');

            $table->decimal('importo_richiesto_mandato', 15, 2)->nullable()->comment('Importo massimo richiesto nel mandato');
            $table->string('scopo_finanziamento')->nullable()->comment('Scopo del finanziamento (es. Acquisto Prima Casa, Liquidità)');  // Es: Acquisto Prima Casa, Liquidità

            // Trasparenza
            $table->date('data_consegna_trasparenza')->nullable()->comment('Deve essere <= data_firma');

            $table->enum('stato', ['attivo', 'concluso_con_successo', 'scaduto', 'revocato'])->default('attivo')->comment('Stato del mandato');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_mandates');
    }
};
