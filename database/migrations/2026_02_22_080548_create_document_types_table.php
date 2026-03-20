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
        Schema::create('document_types', function (Blueprint $table) {
            $table->comment("Tabella di lookup globale (Senza Tenant): Tipologie di documenti riconosciuti per l'Adeguata Verifica.");
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->string('name')->nullable()->comment('Nome documento');
            $table->string('description')->nullable()->comment('Descrizione aggiuntiva');
            $table->string('code')->nullable()->comment('Codice univoco documento menomico es CI = Carta Identita VISURA = Visura aziendale CCIA');
            $table->string('codegroup')->nullable()->comment('Raggruppa documenti simili es. Documento di riconoscimento');
            $table->string('slug')->unique();  // es: "foglio-informativo"
            $table->string('regex_pattern')->nullable();  // La regex per il match automatico
            $table->integer('priority')->default(0);  // Per l'ordine di esecuzione delle regex
            $table->string('phase')->nullable()->comment('Fase di processo - es: "Pre-contrattuale", "Post-contrattuale"');
            $table->boolean('is_person')->default(true)->comment('Documento inerente Persona o azienda');
            $table->boolean('is_signed')->default(false)->comment('Indica se il documento deve essere firmato');
            $table->boolean('is_monitored')->default(false)->comment('Indica se la scadenza documento deve essere monitorata nel tempo');
            $table->boolean('is_company')->default(false)->nullable();
            $table->boolean('is_employee')->default(false)->nullable();
            $table->boolean('is_agent')->default(false)->nullable();
            $table->boolean('is_principal')->default(false)->nullable();
            $table->boolean('is_client')->default(false)->nullable();
            $table->boolean('is_practice')->default(false)->nullable();

            $table->integer('duration')->nullable()->comment('Validità dal rilascio in giorni');
            $table->string('emitted_by')->nullable()->comment('Ente di rilascio');
            $table->boolean('is_sensible')->default(false)->comment('Indica se contiene dati sensibili');
            $table->boolean('is_template')->default(false)->comment('Indica se forniamo noi il documento');
            $table->boolean('is_stored')->default(false)->comment('Indica se il documento deve avere conservazione sostitutiva');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
