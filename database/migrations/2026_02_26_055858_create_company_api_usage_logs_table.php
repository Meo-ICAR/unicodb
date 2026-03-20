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
        Schema::create('company_api_usage_logs', function (Blueprint $table) {
            $table->comment('API usage tracking and rate limiting for company API access.');
            $table->id()->comment('ID univoco log API azienda');

            // Relazioni
            $table->foreignUuid('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Campi del log
            $table->string('service_type')->comment('Tipo di servizio API es. OCR, Signature, etc.');
            $table->decimal('software_cost', 10, 4)->default(0)->comment("Costo reale dell'API");
            $table->decimal('charged_credits', 10, 2)->default(0)->comment('Crediti addebitati al tenant');
            $table->string('status')->default('pending')->comment('Stato della chiamata API');

            // Metadati aggiuntivi
            $table->json('request_data')->nullable()->comment('Dati della richiesta API');
            $table->json('response_data')->nullable()->comment('Dati della risposta API');
            $table->text('error_message')->nullable()->comment('Messaggio di errore se presente');
            $table->integer('response_time_ms')->nullable()->comment('Tempo di risposta in millisecondi');

            $table->timestamps();

            // Indici
            $table->index(['company_id', 'service_type']);
            $table->index(['company_id', 'created_at']);
            $table->index(['service_type', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_api_usage_logs');
    }
};
