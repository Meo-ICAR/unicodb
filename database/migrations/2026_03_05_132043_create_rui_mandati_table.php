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
        Schema::create('rui_mandati', function (Blueprint $table) {
            $table->comment('Tabella mandati RUI (Registro Unico degli Intermediari)');
            $table->id()->comment('ID autoincrementante');
            $table->string('oss')->comment('Codice OSS')->nullable();
            $table->string('matricola', 50)->comment('Matricola intermediario')->nullable();
            $table->string('codice_compagnia', 20)->comment('Codice compagnia')->nullable();
            $table->string('ragione_sociale', 255)->comment('Ragione sociale compagnia')->nullable();
            $table->timestamps();
            
            $table->index('matricola');
            $table->index('codice_compagnia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_mandati');
    }
};
