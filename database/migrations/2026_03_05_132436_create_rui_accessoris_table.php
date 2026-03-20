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
        Schema::create('rui_accessoris', function (Blueprint $table) {
            $table->comment('Tabella collaboratori accessorii RUI (Registro Unico degli Intermediari)');
            $table->id()->comment('ID autoincrementante');
            $table->string('numero_iscrizione_e', 50)->comment('Numero iscrizione E')->nullable();
            $table->string('ragione_sociale', 255)->comment('Ragione sociale')->nullable();
            $table->string('cognome_nome', 255)->comment('Cognome e nome')->nullable();
            $table->string('sede_legale', 255)->comment('Sede legale')->nullable();
            $table->date('data_nascita')->comment('Data di nascita')->nullable();
            $table->string('luogo_nascita', 255)->comment('Luogo di nascita')->nullable();
            $table->timestamps();
            
            $table->index('numero_iscrizione_e');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_accessoris');
    }
};
