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
        Schema::create('comunes', function (Blueprint $table) {
            $table->comment('Comuni italiani ISTAT');
            $table->bigIncrements('id')->comment('ID univoco comune');
            $table->string('codice_regione', 3)->index()->comment('Codice regione ISTAT');
            $table->string('codice_unita_territoriale', 6)->comment('Codice unità territoriale ISTAT');
            $table->string('codice_provincia_storico', 3)->index()->comment('Codice provincia storica');
            $table->string('progressivo_comune', 3)->comment('Progressivo comune ISTAT');
            $table->string('codice_comune_alfanumerico', 6)->comment('Codice comune alfanumerico ISTAT');
            $table->string('denominazione')->comment('Denominazione ufficiale comune');
            $table->string('denominazione_italiano')->comment('Denominazione in italiano');
            $table->string('denominazione_altra_lingua')->nullable()->comment('Denominazione in altra lingua');
            $table->string('codice_ripartizione_geografica', 1)->comment('Codice ripartizione geografica');
            $table->string('ripartizione_geografica')->comment('Ripartizione geografica (Nord, Centro, Sud, Isole)');
            $table->string('denominazione_regione')->index()->comment('Denominazione regione');
            $table->string('denominazione_unita_territoriale')->comment('Denominazione unità territoriale');
            $table->string('tipologia_unita_territoriale')->comment('Tipologia unità territoriale');
            $table->boolean('capoluogo_provincia')->default(false)->comment('Se è capoluogo di provincia');
            $table->string('sigla_automobilistica', 2)->index()->comment('Sigla targa automobilistica');
            $table->string('codice_comune_numerico', 6)->comment('Codice comune numerico ISTAT');
            $table->string('codice_comune_110_province', 6)->comment('Codice comune per province 110');
            $table->string('codice_comune_107_province', 6)->comment('Codice comune per province 107');
            $table->string('codice_comune_103_province', 6)->comment('Codice comune per province 103');
            $table->string('codice_catastale', 4)->comment('Codice catastale comune');
            $table->string('codice_nuts1_2021', 3)->comment('Codice NUTS1 2021');
            $table->string('codice_nuts2_2021', 6)->comment('Codice NUTS2 2021');
            $table->string('codice_nuts3_2021', 6)->comment('Codice NUTS3 2021');
            $table->string('codice_nuts1_2024', 3)->comment('Codice NUTS1 2024');
            $table->string('codice_nuts2_2024', 6)->comment('Codice NUTS2 2024');
            $table->string('codice_nuts3_2024', 6)->comment('Codice NUTS3 2024');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comunes');
    }
};
