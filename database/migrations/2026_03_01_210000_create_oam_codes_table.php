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
        Schema::create('oam_codes', function (Blueprint $table) {
            $table->comment('Codici rilievi OAM');
            $table->id()->comment('ID univoco codice OAM');
            $table->string('code')->comment('Codice identificativo OAM');
            $table->enum('fase', ['1-Procacciamento', '2-Trasparenza', '3-Mediazione'])->comment('Fase del rilievo OAM');
            $table->text('name')->comment('Descrizione codice OAM');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oam_codes');
    }
};
