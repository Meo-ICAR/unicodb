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
        Schema::create('company_branches', function (Blueprint $table) {
            $table->comment('Anagrafica delle sedi operative e legali delle società di mediazione con relativi referenti.');
            $table->increments('id')->comment('ID univoco filiale');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            $table->string('name')->comment('Nome della sede (es. Sede Centrale, Filiale Milano Nord)');
            $table->boolean('is_main_office')->default(false)->comment("Indica se questa è la sede legale/principale dell'agenzia");
            $table->string('manager_first_name', 100)->nullable()->comment('Nome del referente/responsabile della sede');
            $table->string('manager_last_name', 100)->nullable()->comment('Cognome del referente/responsabile della sede');
            $table->string('manager_tax_code', 16)->nullable()->comment('Codice Fiscale del referente della sede');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data creazione sede');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Data ultimo aggiornamento sede');

            $table->index(['company_id', 'is_main_office'], 'idx_company_main');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_branches');
    }
};
