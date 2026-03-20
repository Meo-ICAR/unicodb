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
        Schema::create('failed_import_rows', function (Blueprint $table): void {
            $table->comment('Log errori importazioni dati');
            $table->id()->comment('ID univoco riga importazione fallita');
            $table->json('data')->comment('Dati originali della riga fallita in formato JSON');
            $table->foreignId('import_id')->constrained()->cascadeOnDelete()->comment('Riferimento importazione principale');
            $table->text('validation_error')->nullable()->comment('Messaggio errore validazione');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_import_rows');
    }
};
