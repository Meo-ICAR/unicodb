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
        Schema::create('rui', function (Blueprint $table) {
            $table->comment('Tabella dati RUI (Registro Unico degli Intermediari)');

            // Use numero_iscrizione_rui as primary key (not auto-increment)
            $table->string('numero_iscrizione_rui', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->comment('Numero iscrizione RUI');

            $table->string('oss')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Codice OSS');
            $table->tinyInteger('inoperativo')->default(0)->comment('Stato inoperativo');
            $table->date('data_inizio_inoperativita')->nullable()->comment('Data inizio inoperatività');
            $table->date('data_iscrizione')->nullable()->comment('Data iscrizione RUI');
            $table->string('cognome_nome', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Cognome e Nome');
            $table->string('stato', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Stato');
            $table->string('comune_nascita', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Comune di nascita');
            $table->date('data_nascita')->nullable()->comment('Data di nascita');
            $table->string('ragione_sociale', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Ragione sociale');
            $table->string('provincia_nascita', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Provincia di nascita');
            $table->string('titolo_individuale_sez_a', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Titolo individuale Sezione A');
            $table->string('attivita_esercitata_sez_a', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Attività esercitata Sezione A');
            $table->string('titolo_individuale_sez_b', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Titolo individuale Sezione B');
            $table->string('attivita_esercitata_sez_b', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Attività esercitata Sezione B');
            $table->timestamps();
            $table->unsignedBigInteger('rui_section_id')->nullable()->comment('ID sezione RUI');

            // Set primary key to numero_iscrizione_rui as specified
            $table->primary('numero_iscrizione_rui');

            // Add composite index for fast lookups
            $table->index(['cognome_nome', 'ragione_sociale'], 'idx_rui_names');

            // Foreign key constraint
            $table->foreign('rui_section_id')->references('id')->on('rui_sections')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui');
    }
};
