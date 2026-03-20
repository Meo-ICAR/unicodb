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
        Schema::create('principals', function (Blueprint $table) {
            $table->comment('Tabella globale delle banche ed enti eroganti convenzionati.');
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->string('name')->comment("Nome dell'istituto bancario o finanziaria (es. Intesa Sanpaolo, Compass)");
            $table->string('abi', 30)->nullable()->comment('Abi per banche o numero RUI ISVASS');
            $table->string('abi_name')->nullable()->comment('Nome ufficiale banca');
            $table->date('stipulated_at')->nullable()->comment('Data stipula contratto convenzione');
            $table->date('dismissed_at')->nullable()->comment('Data cessazione rapporto convenzione');
            $table->string('vat_number', 13)->nullable()->comment("Partita IVA dell'istituto");
            $table->string('vat_name')->nullable()->comment('Ragione sociale fiscale');
            $table->string('type', 30)->nullable()->comment('Banca / Assicurazione / Utility');
            $table->string('oam', 30)->nullable()->comment('Codice di iscrizione OAM');
            $table->string('oam_name')->nullable()->comment('Denominazione  OAM');
            $table->date('oam_at')->nullable()->comment('Data iscrizione OAM');
            $table->string('numero_iscrizione_rui', 50)->nullable()->comment('Numero iscrizione OAM');
            $table->string('ivass', 30)->nullable()->comment('Codice di iscrizione IVASS');
            $table->date('ivass_at')->nullable()->comment('Data iscrizione IVASS');
            $table->string('ivass_name')->nullable()->comment('Denominazione  OAM');
            $table->enum('ivass_section', [
                'A',
                'B',
                'C',
                'D',
                'E',
            ])->nullable()->comment('Sezione IVASS');
            $table->boolean('is_active')->default(true)->comment('Indica se la banca è attualmente convenzionata');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            $table->string('mandate_number', 100)->nullable()->comment('Numero di protocollo o identificativo del contratto di mandato');
            $table->date('start_date')->nullable()->comment('Data di decorrenza del mandato');
            $table->date('end_date')->nullable()->comment('Data di scadenza (NULL se a tempo indeterminato)');
            $table->boolean('is_exclusive')->nullable()->default(false)->comment("Indica se il mandato prevede l'esclusiva per quella categoria");
            $table->enum('status', ['ATTIVO', 'SCADUTO', 'RECEDUTO', 'SOPESO'])->nullable()->default('ATTIVO')->comment('Stato operativo del mandato');
            $table->text('notes')->nullable()->comment('Note su provvigioni particolari o patti specifici');
            $table
                ->enum('principal_type', ['--', 'banca', 'agente_assicurativo', 'agente_captive'])
                ->default('banca')
                ->nullable()
                ->comment('Tipologia del mandante');
            $table
                ->enum('submission_type', ['--', 'accesso portale', 'inoltro', 'entrambi'])
                ->default('accesso portale')
                ->nullable()
                ->comment('Modalita inoltro pratiche');
            $table->foreign(['company_id'], 'principals_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('no action');
            $table->string('website')->nullable()->comment('sito web');
            $table->string('portalsite')->nullable()->comment('Portale pratiche');
            $table->string('contoCOGE')->nullable()->comment('Conto COGE');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('principals');
    }
};
