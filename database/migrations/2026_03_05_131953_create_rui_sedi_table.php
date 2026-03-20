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
        Schema::create('rui_sedi', function (Blueprint $table) {
            $table->comment('Tabella sedi RUI (Registro Unico degli Intermediari)');
            $table->id()->comment('ID autoincrementante');
            $table->string('oss')->comment('Codice OSS')->nullable();
            $table->string('numero_iscrizione_int', 50)->comment('Numero iscrizione intermediario')->nullable();
            $table->string('tipo_sede', 100)->comment('Tipo sede')->nullable();
            $table->string('comune_sede', 100)->comment('Comune sede')->nullable();
            $table->string('provincia_sede', 50)->comment('Provincia sede')->nullable();
            $table->string('cap_sede', 10)->comment('CAP sede')->nullable();
            $table->string('indirizzo_sede', 255)->comment('Indirizzo sede')->nullable();
            $table->timestamps();
            
            $table->index('numero_iscrizione_int');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_sedi');
    }
};
