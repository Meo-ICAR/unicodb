<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * tipo_prodotto
 * Cessione
 * Prestito
 * Delega
 * Pignoramento
 * Mutuo
 * Altro
 * Aziendale
 * TFS
 * Polizza
 * Utenza
 * Microcredito
 * ""
 * PRESTITO AZIENDALE
 * ALTRA DELEGAZIONE IMPORTO CONTENUTO
 * CASSA MUTUA
 * ASSICURAZIONE
 * CHIROGRAFARIO
 * LEASING
 * IPOTECARIO
 */
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('practice_scopes', function (Blueprint $table) {
            $table->comment('Tabella tipologia finanziamento');
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->string('name')->comment('Es. Mutui Ipotecari, Cessioni del Quinto, Prestiti Personali')->nullable();
            $table->string('tipo_prodotto')->comment('Cessione Prestito Delega Pignoramento Mutuo Altro Aziendale TFS Polizza Utenza Microcredito PRESTITO AZIENDALE ALTRA DELEGAZIONE IMPORTO CONTENUTO CASSA MUTUA ASSICURAZIONE CHIROGRAFARIO LEASING IPOTECARIO')->nullable();
            $table->string('code')->nullable();
            $table->string('oam_code')->nullable();
            $table
                ->boolean('is_oneclient')
                ->default(true)
                ->comment('Finanziamento mono cliente')
                ->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data di creazione');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Ultima modifica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_scopes');
    }
};
