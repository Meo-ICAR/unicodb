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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->comment('Registro storico di tutte le chiamate API effettuate per monitoraggio e risoluzione problemi.');
            $table->bigIncrements('id')->comment('ID univoco del log API');
            $table->unsignedInteger('api_configuration_id')->comment('Riferimento alla configurazione usata');
            $table->string('api_loggable_type')->nullable()->comment('Classe del Modello collegato');
            $table->string('api_loggable_id', 36)->nullable()->comment('ID del Modello (VARCHAR 36)');
            $table->string('endpoint')->comment("L'endpoint specifico chiamato");
            $table->enum('method', ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'])->comment('Metodo HTTP della chiamata');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->json('request_payload')->nullable()->comment('Dati inviati');
            $table->json('response_payload')->nullable()->comment('Dati ricevuti');
            $table->integer('status_code')->nullable()->comment('Codice HTTP (es. 200, 401, 500)');
            $table->integer('execution_time_ms')->nullable()->comment('Tempo di risposta in millisecondi');
            $table->text('error_message')->nullable()->comment("Descrizione dell'errore se fallito");
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data e ora della chiamata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
