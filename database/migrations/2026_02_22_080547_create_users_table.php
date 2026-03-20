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
        Schema::create('users', function (Blueprint $table) {
            $table->comment('Utenti del sistema: SuperAdmin, Titolari, Agenti e Backoffice.');
            $table->id();  // Crea un BIGINT UNSIGNED automaticamente
            // Modo corretto e moderno (Laravel 10/11/12)
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            $table->string('name')->comment("Nome e Cognome dell'utente");
            $table->string('email')->unique()->comment('Email usata per il login');
            $table->timestamp('email_verified_at')->nullable()->comment('Data verifica email');
            $table->string('password')->comment('Password hashata tramite bcrypt/argon2');
            $table->rememberToken()->comment('Token per la sessione "Ricordami"');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data registrazione utente');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Ultimo aggiornamento profilo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
