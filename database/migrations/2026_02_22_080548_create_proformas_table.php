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
        Schema::create('proformas', function (Blueprint $table) {
            $table->comment('Proforma mensili generati dal sistema per calcolare compensi e ritenute Enasarco degli agenti.');
            $table->increments('id')->comment('ID intero autoincrementante');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            $table->unsignedInteger('agent_id')->comment("L'agente beneficiario delle provvigioni");
            $table->string('name')->nullable()->comment('Riferimento documento (es. Proforma 01/2026 - Rossi Mario)');
            $table->string('commission_label')->nullable();
            $table->decimal('total_commissions', 10)->nullable()->comment('Totale provvigioni lorde maturate nel periodo');
            $table->decimal('enasarco_retained', 10)->nullable()->comment("Quota Enasarco trattenuta dall'agenzia (50% del totale contributo)");
            $table->decimal('remburse', 10)->nullable();
            $table->string('remburse_label')->nullable();
            $table->decimal('contribute', 10)->nullable();
            $table->string('contribute_label')->nullable();
            $table->decimal('refuse', 10)->nullable();
            $table->string('refuse_label')->nullable();
            $table->decimal('net_amount', 10)->nullable()->comment("Importo netto da liquidare all'agente");
            $table->integer('month')->nullable()->comment('Mese di competenza della liquidazione (1-12)');
            $table->integer('year')->nullable()->comment('Anno di competenza');
            $table->enum('status', ['INSERITO', 'INVIATO', 'ANNULLATO', 'FATTURATO', 'PAGATO', 'STORICO'])->default('INSERITO');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data di generazione del proforma');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Ultima modifica prima della fatturazione definitiva');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proformas');
    }
};
