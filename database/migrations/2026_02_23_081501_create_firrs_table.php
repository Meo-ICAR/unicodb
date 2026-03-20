<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('firrs', function (Blueprint $table) {
            $table->comment('Aliquote per il calcolo dell\'Indennità Risoluzione Rapporto (FIRR)');
            $table->id();
            $table->decimal('minimo', 10, 2)->nullable()->comment('Importo minimo');
            $table->decimal('massimo', 10, 2)->nullable()->comment('Importo massimo');
            $table->decimal('aliquota', 5, 2)->nullable()->comment('Aliquota FIRR');
            $table->integer('competenza')->default(2025)->comment('Anno di competenza');
            $table->enum('enasarco', ['monomandatario', 'plurimandatario', 'societa', 'no'])
                ->default('plurimandatario')
                ->comment('Tipo mandato ENASARCO');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('firrs');
    }
};
