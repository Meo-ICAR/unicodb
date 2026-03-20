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
        Schema::create('oams', function (Blueprint $table) {
            $table->comment('Elenco OAM Mediatori e Agenti');
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('autorizzato_ad_operare');
            $table->string('persona');
            $table->string('codice_fiscale', 16)->index();
            $table->string('domicilio_sede_legale');
            $table->string('elenco')->index();
            $table->string('numero_iscrizione');
            $table->date('data_iscrizione')->nullable();
            $table->string('stato')->index();
            $table->date('data_stato')->nullable();
            $table->text('causale_stato_note')->nullable();
            $table->timestamps();

            $table->unique(['codice_fiscale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oams');
    }
};
