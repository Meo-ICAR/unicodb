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
        Schema::table('rui_websites', function (Blueprint $table) {
            // Rimuovi il constraint unique da numero_iscrizione_rui
            $table->dropUnique('rui_websites_numero_iscrizione_rui_unique');

            // Aggiungi un indice per performance
            $table->index('numero_iscrizione_rui');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rui_websites', function (Blueprint $table) {
            // Ripristina il constraint unique
            $table->unique('numero_iscrizione_rui');

            // Rimuovi l'indice
            $table->dropIndex(['numero_iscrizione_rui']);
        });
    }
};
