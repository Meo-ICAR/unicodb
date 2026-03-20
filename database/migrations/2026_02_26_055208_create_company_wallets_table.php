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
        Schema::create('company_wallets', function (Blueprint $table) {
            $table->comment('Wallet azienda per utilizzo API');
            $table->id()->comment('ID univoco wallet azienda');

            // Relazioni
            $table->foreignUuid('company_id')->constrained('companies')->onDelete('cascade');
            $table->unsignedInteger('software_application_id');
            $table->foreign('software_application_id')->references('id')->on('software_applications')->onDelete('cascade');

            // Campi del wallet
            $table->decimal('credit', 15, 2)->default(0)->comment('Credito a disposizione');
            $table->date('start_date')->comment('Data di inizio validità');
            $table->date('trial_date')->nullable()->comment('Data fine periodo trial');
            $table->boolean('is_active')->default(true)->comment('Wallet attivo');
            $table->string('name')->comment('Nome del wallet/servizio');
            $table->text('description')->nullable()->comment('Descrizione del servizio');

            $table->timestamps();

            // Indici
            $table->unique(['company_id', 'software_application_id'], 'unique_company_software');
            $table->index(['company_id', 'is_active']);
            $table->index(['software_application_id', 'is_active']);
            $table->index(['start_date', 'trial_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_wallets');
    }
};
