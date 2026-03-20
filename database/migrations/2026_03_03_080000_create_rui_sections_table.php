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
        Schema::create('rui_sections', function (Blueprint $table) {
            $table->comment('Tabella sezioni RUI (Registro Unico degli Intermediari)');
            $table->id()->comment('ID autoincrementante');
            $table->string('sezione', 5)->unique()->comment('Codice sezione (A, B, C, D, E, F)');
            $table->string('categoria', 50)->comment('Nome categoria della sezione');
            $table->text('descrizione')->comment('Descrizione dettagliata della sezione');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_sections');
    }
};
