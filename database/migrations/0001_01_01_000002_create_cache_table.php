<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCacheTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cache', function (Blueprint $table) {
            $table->comment('Application cache storage for Laravel cache system.');
            $table->string('key')->primary()->comment('Chiave univoca cache');
            $table->mediumText('value')->comment('Valore memorizzato in cache');
            $table->integer('expiration')->comment('Timestamp scadenza cache');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->comment('Cache locks for atomic cache operations in Laravel.');
            $table->string('key')->primary()->comment('Chiave univoca lock cache');
            $table->string('owner')->comment('Proprietario del lock');
            $table->integer('expiration')->comment('Timestamp scadenza lock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
}
