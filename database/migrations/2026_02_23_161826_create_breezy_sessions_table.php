<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('breezy_sessions', function (Blueprint $table) {
            $table->comment('Sessions for Laravel Breezy two-factor authentication.');
            $table->id()->comment('ID univoco sessione breezy');
            $table->morphs('authenticatable');
            $table->string('panel_id')->nullable()->comment('ID del pannello Filament');
            $table->string('guard')->nullable()->comment('Guard di autenticazione');
            $table->string('ip_address', 45)->nullable()->comment('Indirizzo IP del client');
            $table->text('user_agent')->nullable()->comment('User agent del browser');
            $table->timestamp('expires_at')->nullable()->comment('Data di scadenza sessione');
            $table->text('two_factor_secret')->nullable()->comment('Secret per autenticazione a due fattori');
            $table->text('two_factor_recovery_codes')->nullable()->comment('Codici di recupero 2FA');
            $table->timestamp('two_factor_confirmed_at')->nullable()->comment('Data conferma 2FA');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('breezy_sessions');
    }
};
