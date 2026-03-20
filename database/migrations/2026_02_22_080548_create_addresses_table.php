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
        Schema::create('addresses', function (Blueprint $table) {
            $table->comment('Tabella polimorfica per salvare molteplici indirizzi associabili a Company, Clienti o Utenti.');
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->string('addressable_type')->comment('Classe del Modello collegato (es. App\\Models\\Client)');
            $table->string('addressable_id', 36)->comment('ID del Modello (VARCHAR 36 per supportare sia UUID che Integer)');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->string('street')->nullable()->comment('Via e numero civico');
            $table->string('city')->nullable()->comment('Città o Comune');
            $table->string('zip_code', 20)->nullable()->comment('CAP (Codice di Avviamento Postale)');
            $table->integer('address_type_id')->nullable()->comment('Relazione con tipologia indirizzo');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data inserimento indirizzo');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Data ultimo aggiornamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
