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
        Schema::create('rui_sezds', function (Blueprint $table) {
            $table->comment('Tabella responsabili distribuzione Sezione D RUI');
            $table->id()->comment('ID autoincrementante');
            $table->string('numero_iscrizione_d', 50)->comment('Numero iscrizione D')->nullable();
            $table->string('ragione_sociale', 255)->comment('Ragione sociale')->nullable();
            $table->string('cognome_nome_responsabile', 255)->comment('Cognome e nome responsabile')->nullable();
            $table->timestamps();
            
            $table->index('numero_iscrizione_d');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_sezds');
    }
};
