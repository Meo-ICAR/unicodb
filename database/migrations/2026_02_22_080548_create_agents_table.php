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
        Schema::create('agents', function (Blueprint $table) {
            $table->comment('Tabella globale agenti convenzionati.');
            $table->increments('id')->comment('ID univoco agente');
            $table->string('name')->comment('Nome agente');
            $table->string('description')->nullable()->comment('Descrizione');
            $table
                ->enum('supervisor_type', ['no', 'si', 'filiale'])
                ->nullable()
                ->default('no')
                ->comment('Se supervisore indicare e specificare se di filiale');
            $table->string('oam', 30)->nullable()->comment('Oam');
            $table->date('oam_at')->nullable()->comment('Data iscrizione OAM');
            $table->string('oam_name')->nullable()->comment('Denominazione sociale registrata in OAM');
            $table->string('numero_iscrizione_rui', 50)->nullable()->comment('Numero iscrizione OAM');
            $table->string('ivass', 30)->nullable()->comment('Codice di iscrizione IVASS');
            $table->date('ivass_at')->nullable()->comment('Data iscrizione IVASS');
            $table->string('ivass_name')->nullable()->comment('Denominazione  IVASS');
            $table->enum('ivass_section', [
                'A',
                'B',
                'C',
                'D',
                'E',
            ])->nullable()->comment('Sezione IVASS');
            $table->date('stipulated_at')->nullable()->comment('Data stipula contratto collaborazione');
            $table->date('dismissed_at')->nullable()->comment('Data cessazione rapporto');
            $table->string('type', 30)->nullable()->comment('Agente / Mediatore / Consulente / Call Center ');
            $table->decimal('contribute', 10)->nullable()->comment('Importo contributo fisso/quota');
            $table->integer('contributeFrequency')->nullable()->default(1)->comment('Frequenza contributo (mesi)');
            $table->date('contributeFrom')->nullable()->comment('Data inizio addebito contributi');
            $table->decimal('remburse', 10)->nullable()->comment('Importo rimborsi spese concordati');
            $table->string('vat_number', 16)->nullable()->comment('Partita IVA Agente');
            $table->string('vat_name')->nullable()->comment('Ragione Sociale Fiscale');
            $table->string('enasarco')->nullable()->comment('Enasarco no / monomandatario / plurimandatario / societa');
            $table->boolean('is_active')->default(true)->comment('Indica se la banca è attualmente convenzionata');
            $table->string('contoCOGE')->nullable()->comment('Conto COGE');
            $table->unsignedInteger('company_branch_id')->nullable()->comment('Filiale di riferimento');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            //             $table->char('company_id', 36)->comment('Tenant di appartenenza');
            $table
                ->foreignId('user_id')
                ->nullable()
                ->comment("ID dell'utente collegato")
                ->constrained('users')  // Indica esplicitamente la tabella users
                ->onDelete('set null');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
