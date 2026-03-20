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
        Schema::create('mandates', function (Blueprint $table) {
            $table->comment("Contratti di mandato che legano l'agenzia agli Istituti Bancari.");
            $table->increments('id')->comment('ID univoco del mandato');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            $table->unsignedInteger('principal_id')->comment('Banca o Istituto mandante');
            $table->string('mandate_number', 100)->nullable()->comment('Numero di protocollo o identificativo del contratto di mandato');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->date('start_date')->nullable()->comment('Data di decorrenza del mandato');
            $table->date('end_date')->nullable()->comment('Data di scadenza (NULL se a tempo indeterminato)');
            $table->boolean('is_exclusive')->nullable()->default(false)->comment("Indica se il mandato prevede l'esclusiva per quella categoria");
            $table->enum('status', ['ATTIVO', 'SCADUTO', 'RECEDUTO', 'SOPESO'])->nullable()->default('ATTIVO')->comment('Stato operativo del mandato');
            $table->string('contract_file_path')->nullable()->comment('Riferimento al PDF del contratto firmato');
            $table->text('notes')->nullable()->comment('Note su provvigioni particolari o patti specifici');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandates');
    }
};
