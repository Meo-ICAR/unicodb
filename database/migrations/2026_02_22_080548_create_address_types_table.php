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
        Schema::create('address_types', function (Blueprint $table) {
            $table->comment('Tipologie di indirizzi (residenza, domicilio, sede legale, etc.).');
            $table->integer('id', true)->comment('ID univoco tipo indirizzo');
            $table->string('name')->nullable()->comment('Descrizione');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address_types');
    }
};
