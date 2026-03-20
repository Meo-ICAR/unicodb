<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('venasarcotot', function (Blueprint $table) {
            $table->comment('Totali ENASARCO per produttore');
            $table->id();
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade
            $table->string('produttore')->nullable()->comment('Ragione sociale del referente');
            $table->decimal('montante', 37, 2)->nullable()->comment('Montante provvigioni');
            $table->decimal('contributo', 47, 8)->nullable()->comment('Contributo ENASARCO');
            $table->string('X', 2)->nullable()->comment('Flag X');
            $table->decimal('imposta', 47, 8)->nullable()->comment('Imposta sostitutiva');
            $table->decimal('firr', 37, 2)->nullable()->comment('Importo FIRR');
            $table->integer('competenza')->nullable()->comment('Anno di competenza');
            $table
                ->enum('enasarco', ['monomandatario', 'plurimandatario', 'societa', 'no'])
                ->default('plurimandatario')
                ->comment('Tipo di mandato ENASARCO');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venasarcotot');
    }
};
