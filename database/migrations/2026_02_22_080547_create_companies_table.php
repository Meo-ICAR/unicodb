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
        Schema::create('companies', function (Blueprint $table) {
            $table->comment('Tabella principale dei Tenant (Società di Mediazione Creditizia).');
            $table->char('id', 36)->primary()->comment('UUID v4 generato da Laravel (Chiave Primaria)');
            $table->string('name')->comment('Ragione Sociale della società di mediazione');
            $table->string('vat_number', 50)->nullable()->comment("Partita IVA o Codice Fiscale dell'agenzia");
            $table->string('vat_name', 50)->nullable()->comment('Denominazione fiscale per fatturazione');
            $table->string('oam', 50)->nullable()->comment('Numero iscrizione OAM');
            $table->date('oam_at')->nullable()->comment('Data iscrizione OAM ');
            $table->string('oam_name')->nullable()->comment('Nome registrato negli elenchi OAM');
            $table->string('ivass', 30)->nullable()->comment('Codice di iscrizione IVASS');
            $table->date('ivass_at')->nullable()->comment('Data iscrizione IVASS');
            $table->string('ivass_name')->nullable()->comment('Denominazione  IVASS');
            $table->enum('ivass_section', [
                'A',
                'B',
                'C',
                'D',
                'E',
            ])->nullable()->comment('Sezione IVASS');
            $table->string('sponsor')->nullable()->comment('Provenienza del ns. cliente');
            $table->unsignedInteger('company_type_id')->nullable()->comment('Tipo forma giuridica della società');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data di creazione del tenant');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Data di ultima modifica');

            $table->foreign('company_type_id')->references('id')->on('company_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
