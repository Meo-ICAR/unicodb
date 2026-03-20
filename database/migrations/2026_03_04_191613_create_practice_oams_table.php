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
        Schema::create('practice_oams', function (Blueprint $table) {
            $table->id();

            // ID fields
            $table->unsignedInteger('practice_id')->nullable()->comment('ID della pratica associata');
            $table->unsignedBigInteger('oam_code_id')->nullable()->comment('ID del codice OAM associato');
            $table->string('oam_code')->nullable()->comment('Codice OAM prodotto');

            $table->string('oam_name', 255)->nullable()->comment('Codice OAM prodotto');
            $table->string('principal_name', 255)->nullable()->comment('Nome intermediario');
            $table->tinyInteger('is_notconvenctioned')->default(0)->comment('Pratica convenzionata');
            $table->tinyInteger('is_previous')->default(0)->comment('Pratica precedente');

            // Aggiungo i campi liquidato e liquidato_lavorazione se non esistono
            if (!Schema::hasColumn('practice_oams', 'liquidato')) {
                $table->decimal('liquidato', 10, 2)->nullable()->comment('Importo liquidato');
            }

            if (!Schema::hasColumn('practice_oams', 'liquidato_lavorazione')) {
                $table->decimal('liquidato_lavorazione', 10, 2)->nullable()->comment('Importo liquidato lavorazione');
            }

            // Aggiungo altri campi mancanti
            $table->string('CRM_code')->nullable()->comment('Codice CRM');
            $table->string('practice_name', 255)->nullable()->comment('Nome pratica');
            $table->string('type', 255)->nullable()->comment('Tipo');
            $table->date('inserted_at')->nullable()->comment('Data inserimento');
            $table->date('erogated_at')->nullable()->comment('Data erogazione');

            // Commission fields
            $table->decimal('compenso', 10, 2)->nullable()->comment('Compenso totale');
            $table->decimal('compenso_lavorazione', 10, 2)->nullable()->comment('Compenso lavorazione');
            $table->decimal('erogato', 10, 2)->nullable()->comment('Importo erogato');
            $table->decimal('erogato_lavorazione', 10, 2)->nullable()->comment('Importo erogato lavorazione');
            $table->decimal('compenso_premio', 10, 2)->nullable()->comment('Compenso premio assicurativo');
            $table->decimal('compenso_rimborso', 10, 2)->nullable()->comment('Compenso rimborso spese');
            $table->decimal('compenso_assicurazione', 10, 2)->nullable()->comment('Compenso assicurazione');
            $table->decimal('compenso_cliente', 10, 2)->nullable()->comment('Compenso cliente');
            $table->decimal('storno', 10, 2)->nullable()->comment('Importo storno');

            // Provision fields
            $table->decimal('provvigione', 10, 2)->nullable()->comment('Provvigione totale');
            $table->decimal('provvigione_lavorazione', 10, 2)->nullable()->comment('Provvigione lavorazione');
            $table->decimal('provvigione_premio', 10, 2)->nullable()->comment('Provvigione premio assicurativo');
            $table->decimal('provvigione_rimborso', 10, 2)->nullable()->comment('Provvigione rimborso spese');
            $table->decimal('provvigione_assicurazione', 10, 2)->nullable()->comment('Provvigione assicurazione');
            $table->decimal('provvigione_storno', 10, 2)->nullable()->comment('Provvigione storno');

            // Status fields
            $table->boolean('is_active')->default(1)->comment('Campo per escludere manualmente');
            $table->boolean('is_cancel')->default(0)->comment('Pratica stornata');
            $table->boolean('is_perfected')->default(0)->comment('Pratica perfezionata nel periodo');
            $table->boolean('is_conventioned')->default(0)->comment('Pratica convenzionata');
            $table->boolean('is_notconventioned')->default(0)->comment('Pratica non convenzionata');
            $table->boolean('is_working')->default(1)->comment('PracticeOam is working boolean');

            // Date fields
            $table->date('invoice_at')->nullable()->comment('Data di fatturazione');
            $table->date('start_date')->nullable()->comment('Data di inizio');
            $table->date('perfected_at')->nullable()->comment('Data di perfezionamento');
            $table->date('end_date')->nullable()->comment('Data di fine');
            $table->date('accepted_at')->nullable()->comment('Data inizio autorizzazione');
            $table->date('canceled_at')->nullable()->comment('Data di storno');

            // Other fields
            $table->boolean('is_invoice')->default(0)->comment('Pratica fatturata');
            $table->boolean('is_before')->default(0)->comment('Pratica fatturata');
            $table->boolean('is_after')->default(0)->comment('Pratica fatturata');
            $table->string('name', 255)->nullable()->comment('Mandanti');
            $table->string('tipo_prodotto')->nullable()->comment('Prodotto');
            $table->integer('mese')->nullable()->comment('Mese');
            $table->char('company_id', 36)->nullable()->comment('ID azienda');

            // Indexes
            $table->index('practice_id');
            $table->index('oam_code_id');
            $table->index('company_id');
            $table->index('oam_name');
            $table->index('principal_name');

            // Aggiungo indici per i nuovi campi

            $table->index('CRM_code');
            $table->index('practice_name');
            $table->index('type');

            // Foreign keys
            $table->foreign('practice_id')->references('id')->on('practices');
            $table->foreign('oam_code_id')->references('id')->on('oam_codes');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_oams');
    }
};
