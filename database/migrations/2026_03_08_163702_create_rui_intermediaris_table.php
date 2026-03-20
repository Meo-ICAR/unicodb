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
        Schema::create('rui_intermediaris', function (Blueprint $table) {
            $table->id();
            $table->integer('oss')->comment('OSS field');
            $table->boolean('inoperativo')->default(false)->comment('INOPERATIVO status');
            $table->date('data_inizio_inoperativita')->nullable()->comment('DATA_INIZIO_INOPERATIVITA');
            $table->string('numero_iscrizione_rui', 50)->comment('NUMERO_ISCRIZIONE_RUI');
            $table->date('data_iscrizione')->nullable()->comment('DATA_ISCRIZIONE');
            $table->string('cognome_nome')->comment('COGNOME_NOME');
            $table->string('stato')->comment('STATO');
            $table->string('comune_nascita')->comment('COMUNE_NASCITA');
            $table->date('data_nascita')->nullable()->comment('DATA_NASCITA');
            $table->string('ragione_sociale')->nullable()->comment('RAGIONE_SOCIALE');
            $table->string('provincia_nascita')->comment('PROVINCIA_NASCITA');
            $table->string('titolo_individuale_sez_a')->nullable()->comment('TITOLO_INDIVIDUALE_SEZ_A');
            $table->string('attivita_esercitata_sez_a')->nullable()->comment('ATTIVITA_ESERCITATA_SEZ_A');
            $table->string('titolo_individuale_sez_b')->nullable()->comment('TITOLO_INDIVIDUALE_SEZ_B');
            $table->string('attivita_esercitata_sez_b')->nullable()->comment('ATTIVITA_ESERCITATA_SEZ_B');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_intermediaris');
    }
};
