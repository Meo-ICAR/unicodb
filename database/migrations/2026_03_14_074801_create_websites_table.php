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
        Schema::create('websites', function (Blueprint $table) {
            $table->id()->comment('ID univoco del sito');
            $table->string('websiteable_type')->comment('Tipo di modello associato (es. Company / Agent)')->nullable();
            $table->char('websiteable_id', 36)->nullable();
            $table->char('company_id', 36)->nullable();
            $table->string('name')->comment('Nome del sito (es. Portale Agenti Roma)');
            $table->string('domain')->comment('Dominio o sottodominio (es. agenzia-x.mediaconsulence.it)');
            $table->string('type')->nullable()->comment('Tipologia sito (Vetrina, Portale, Landing)');
            $table->unsignedInteger('principal_id')->nullable()->comment('Mandante di riferimento per landing dedicate');
            $table->boolean('is_active')->default(true)->comment('Stato del sito (online/offline)');
            $table->boolean('is_typical')->default(true)->comment('Sito utilizzato per attività tipica');
            $table->date('privacy_date')->nullable()->comment('Data aggiornamento privacy');
            $table->date('transparency_date')->nullable()->comment('Data aggiornamento trasparenza');
            $table->date('privacy_prior_date')->nullable()->comment('Precedente aggiornamento privacy');
            $table->date('transparency_prior_date')->nullable()->comment('Precedente aggiornamento trasparenza');
            $table->string('url_privacy')->nullable()->comment('URL pagina privacy policy');
            $table->string('url_cookies')->nullable()->comment('URL pagina cookie policy');
            $table->boolean('is_footercompilant')->default(false)->comment('True se il footer è conforme GDPR');
            $table->string('url_transparency')->nullable()->comment('link trasparenza');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['websiteable_type', 'websiteable_id']);
            $table->index('company_id');
            $table->index('domain');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
