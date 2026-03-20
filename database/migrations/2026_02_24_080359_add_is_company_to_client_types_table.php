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
        // La colonna is_company esiste già, non faccio nulla
        // Questa migration è stata resa obsoleta da altre modifiche
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non rimuovo nulla per non rompere altre funzionalità
    }
};
