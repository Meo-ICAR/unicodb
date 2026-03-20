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
        Schema::create('rui_agentis', function (Blueprint $table) {
            $table->comment('Tabella agenti RUI (Registro Unico degli Intermediari)');
            $table->id()->comment('ID autoincrementante');
            $table->string('numero_iscrizione_d', 50)->comment('Numero iscrizione D')->nullable();
            $table->string('numero_iscrizione_a', 50)->comment('Numero iscrizione A')->nullable();
            $table->datetime('data_conferimento')->comment('Data conferimento')->nullable();
            $table->string('codice_compagnia', 20)->comment('Codice compagnia')->nullable();
            $table->string('ragione_sociale', 255)->comment('Ragione sociale compagnia')->nullable();
            $table->timestamps();
            
            $table->index('numero_iscrizione_d');
            $table->index('numero_iscrizione_a');
            $table->index('codice_compagnia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_agentis');
    }
};
