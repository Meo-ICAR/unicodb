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
        Schema::create('principal_scopes', function (Blueprint $table) {
            $table->comment('Tabella pivot: definisce quali comparti operativi sono autorizzati per ogni singolo mandato.');
            $table->unsignedInteger('principal_id')->nullable()->comment('Riferimento al mandato');
            $table->unsignedInteger('practice_scope_id')->nullable()->comment("Riferimento all'ambito (es. Cessione del Quinto, Mutui)");
            $table->string('tipo_prodotto')->comment('Cessione Prestito Delega Pignoramento Mutuo Altro Aziendale TFS Polizza Utenza Microcredito PRESTITO AZIENDALE ALTRA DELEGAZIONE IMPORTO CONTENUTO CASSA MUTUA ASSICURAZIONE CHIROGRAFARIO LEASING IPOTECARIO')->nullable();
            $table->date('start_date')->nullable()->comment('Data di decorrenza del mandato');
            $table->date('end_date')->nullable()->comment('Data di scadenza (NULL se a tempo indeterminato)');
            $table->boolean('is_active')->default(true)->comment('Indica se il scope è attivo')->nullable();
            $table->boolean('is_forced')->default(false)->comment('Indica se il scope è forzato')->nullable();
            $table->string('name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('principal_scopes');
    }
};
