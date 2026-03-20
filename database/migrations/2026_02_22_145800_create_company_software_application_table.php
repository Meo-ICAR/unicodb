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
        Schema::create('company_software_application', function (Blueprint $table) {
            $table->comment('Software applications assigned to companies for service access.');
            $table->id()->comment('ID univoco applicazione software azienda');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade
            $table->unsignedInteger('software_application_id')->comment('ID del software');
            $table->string('status')->default('ATTIVO')->comment("Stato dell'associazione (es. ATTIVO, SOSPESO)");
            $table->string('apikey')->nullable()->comment('API Key per il software');
            $table->decimal('wallet_balance', 10, 2)->nullable()->comment('Saldo del wallet');
            $table->text('notes')->nullable()->comment("Note specifiche per l'azienda");
            $table->timestamps();

            $table->foreign('software_application_id')->references('id')->on('software_applications')->onDelete('cascade');

            $table->unique(['company_id', 'software_application_id'], 'uk_company_software');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_software_application');
    }
};
