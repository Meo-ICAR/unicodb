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
        Schema::create('api_configurations', function (Blueprint $table) {
            $table->comment("Configurazioni tecniche per l'interfacciamento API con software terzi.");
            $table->increments('id')->comment('ID univoco configurazione');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            $table->unsignedInteger('software_application_id')->comment('Software con cui interfacciarsi');
            $table->string('name')->nullable()->comment('Nome mnemonico della connessione');
            $table->string('base_url')->nullable()->comment("URL base dell'API (es. https://api.crmesterno.it/v1)");
            $table->enum('auth_type', ['BASIC', 'BEARER_TOKEN', 'API_KEY', 'OAUTH2'])->nullable()->default('API_KEY')->comment('Metodo di autenticazione');
            $table->text('api_key')->nullable()->comment('Chiave API o Client ID');
            $table->text('api_secret')->nullable()->comment('Segreto API o Client Secret');
            $table->text('access_token')->nullable()->comment('Token di accesso attuale (se OAUTH2 o BEARER)');
            $table->text('refresh_token')->nullable()->comment('Token per il rinnovo della sessione');
            $table->timestamp('token_expires_at')->nullable()->comment('Data di scadenza del token attuale');
            $table->boolean('is_active')->nullable()->default(true)->comment("Indica se l'integrazione è abilitata");
            $table->string('webhook_secret')->nullable()->comment('Chiave per validare i dati in entrata (Webhooks)');
            $table->timestamp('last_sync_at')->nullable()->comment("Data e ora dell'ultima sincronizzazione riuscita");
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data creazione configurazione');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Data ultimo aggiornamento configurazione');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_configurations');
    }
};
