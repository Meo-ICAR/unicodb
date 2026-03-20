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
        Schema::create('rui_cariche', function (Blueprint $table) {
            $table->comment('Tabella cariche RUI (Registro Unico degli Intermediari)');
            $table->id()->comment('ID autoincrementante');
            $table->string('oss')->comment('Codice OSS')->nullable();
            $table->string('numero_iscrizione_rui_pf', 50)->comment('Numero iscrizione RUI persona fisica')->nullable();
            $table->string('numero_iscrizione_rui_pg', 50)->comment('Numero iscrizione RUI persona giuridica')->nullable();
            $table->string('qualifica_intermediario', 255)->comment('Qualifica intermediario')->nullable();
            $table->string('responsabile', 255)->comment('Responsabile')->nullable();
            $table->timestamps();
            
            $table->index('numero_iscrizione_rui_pf');
            $table->index('numero_iscrizione_rui_pg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_cariche');
    }
};
