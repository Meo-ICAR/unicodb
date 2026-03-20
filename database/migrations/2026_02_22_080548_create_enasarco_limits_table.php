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
        Schema::create('enasarco_limits', function (Blueprint $table) {
            $table->comment('Tabella di lookup globale (Senza Tenant): Massimali e minimali annui stabiliti dalla Fondazione Enasarco.');
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->string('name')->comment('Descrizione');
            $table->integer('year')->comment('Anno di riferimento per l\'aliquota');
            $table->decimal('minimal_amount', 10)->comment('Minimale contributivo annuo in Euro');
            $table->decimal('maximal_amount', 10)->comment('Massimale provvigionale annuo in Euro');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data inserimento record');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Data aggiornamento importi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enasarco_limits');
    }
};
