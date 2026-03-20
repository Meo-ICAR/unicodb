<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('principal_contacts', function (Blueprint $table) {
            $table->comment('Rubrica dei referenti presso le banche mandanti per comunicazioni operative e istruttoria.');
            $table->increments('id')->comment('ID univoco contatto mandante');
            $table->unsignedInteger('principal_id')->comment('Riferimento alla banca mandante');
            $table->string('first_name', 100)->comment('Nome del referente bancario');
            $table->string('last_name', 100)->comment('Cognome del referente bancario');
            $table->string('role_title', 150)->nullable()->comment('Ruolo (es. Responsabile Istruttoria, Area Manager, Deliberante)');
            $table->string('department', 100)->nullable()->comment('Dipartimento (es. Ufficio Mutui, Compliance, Estero)');
            $table->string('email')->comment('Email diretta del referente');
            $table->string('phone_office', 50)->nullable()->comment('Telefono ufficio / interno');
            $table->string('phone_mobile', 50)->nullable()->comment('Cellulare aziendale');
            $table->boolean('is_active')->nullable()->default(true)->comment('Indica se il referente è ancora il punto di contatto');
            $table->text('notes')->nullable()->comment('Note utili (es. "Contattare solo per pratiche sopra i 200k")');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['last_name', 'department'], 'idx_principal_contact_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('principal_contacts');
    }
};
