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
        Schema::create('practice_status_lookup', function (Blueprint $table) {
            $table->comment('Tabella lookup per gli stati delle pratiche con colori associati');
            $table->id();
            $table->string('name')->comment('Nome dello stato (es. istruttoria, deliberata, erogata)');
            $table->string('color')->nullable()->comment('Colore del badge per Filament (es. warning, success, danger)');
            $table->string('description')->nullable()->comment('Descrizione dettagliata dello stato');
            $table->boolean('is_active')->default(true)->comment('Se lo stato è utilizzabile');
            $table->integer('sort_order')->default(0)->comment('Ordinamento visualizzazione');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_status_lookup');
    }
};
