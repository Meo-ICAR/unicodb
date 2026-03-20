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
        Schema::create('company_clients', function (Blueprint $table) {
            $table->comment('Pivot table linking companies to their assigned clients.');
            $table->id()->comment('ID univoco relazione azienda-cliente');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade
            $table->unsignedInteger('client_id')->comment('ID del cliente');
            $table->string('role')->default('privacy')->comment('Ruolo privacy per consulenti esterni');
            $table->string('privacy_role')->nullable()->comment('Ruolo Privacy (es. Titolare Autonomo, Responsabile Esterno)');
            $table->text('purpose')->nullable()->comment('Finalità del trattamento');
            $table->text('data_subjects')->nullable()->comment('Categorie di Interessati');
            $table->text('data_categories')->nullable()->comment('Categorie di Dati Trattati');
            $table->string('retention_period')->nullable()->comment('Tempi di Conservazione (Data Retention)');
            $table->string('extra_eu_transfer')->nullable()->comment('Trasferimento Extra-UE');
            $table->text('security_measures')->nullable()->comment('Misure di Sicurezza');
            $table->string('privacy_data')->nullable()->comment('Altri Dati Privacy');

            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->unique(['company_id', 'client_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_clients');
    }
};
