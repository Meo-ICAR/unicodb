<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('socialite_users', function (Blueprint $table) {
            $table->id();

            // Chi ha causato l'anomalia? (Può essere null se è un attacco esterno)
            $table
                ->foreignId('user_id')
                ->nullable()
                ->comment("ID dell'utente collegato")
                ->constrained('users')  // Indica esplicitamente la tabella users
                ->onDelete('set null');

            $table->string('provider');
            $table->string('provider_id');

            $table->timestamps();

            $table->unique([
                'provider',
                'provider_id',
            ]);
        });
    }

    public function down()
    {
        Schema::dropIfExists('socialite_users');
    }
};
