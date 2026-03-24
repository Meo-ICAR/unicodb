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
        Schema::create('o_a_m_soggetti', function (Blueprint $table) {
            $table->id();
            $table->string('denominazione_sociale')->comment('Cognome e Nome / Denominazione Sociale');
            $table->boolean('autorizzato_ad_operare')->default(true)->comment('Autorizzato ad operare');
            $table->string('persona')->comment('Persona (Giuridica/Fisica)');
            $table->string('codice_fiscale', 16)->nullable()->comment('Codice fiscale');
            $table->text('domicilio_sede_legale')->nullable()->comment('Domicilio / Sede Legale');
            $table->string('elenco')->nullable()->comment('Elenco');
            $table->string('numero_iscrizione')->nullable()->comment('N° Iscrizione');
            $table->date('data_iscrizione')->nullable()->comment('Data Iscrizione');
            $table->string('stato')->nullable()->comment('Stato');
            $table->date('data_stato')->nullable()->comment('Data Stato');
            $table->text('causale_stato_note')->nullable()->comment('Causale stato / Note');
            $table->string('check_collaborazione')->nullable()->comment('Check collaborazione');
            $table->string('dipendente_collaboratore_di')->nullable()->comment('Dipendente / Collaboratore di');
            $table->integer('numero_collaborazioni_attive')->default(0)->comment('Numero collaborazioni attive');
            $table->timestamps();

            $table->index('codice_fiscale');
            $table->index('numero_iscrizione');
            $table->index('stato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('o_a_m_soggetti');
    }
};
