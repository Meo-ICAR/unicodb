<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('regulatory_bodies', function (Blueprint $table) {
            $table->comment('Anagrafica delle Autorità di Vigilanza e degli Enti preposti ai controlli normativi.');
            $table->increments('id')->comment('ID univoco dell\'ente');
            $table->string('name')->unique()->comment('Nome dell\'ente (es. OAM - Organismo Agenti e Mediatori, Garante per la Protezione dei Dati Personali)');
            $table->string('acronym', 20)->nullable()->comment('Sigla (es. OAM, GPDP, IVASS)');
            $table->string('official_website')->nullable()->comment('Sito web istituzionale');
            $table->string('pec_address')->nullable()->comment('Indirizzo PEC per comunicazioni legali');
            $table->string('portal_url')->nullable()->comment('URL del portale riservato per invio flussi/segnalazioni');
            $table->string('contact_person', 100)->nullable()->comment('Eventuale referente o dirigente di riferimento');
            $table->string('phone_support', 50)->nullable()->comment('Numero di telefono assistenza/ispettorato');
            $table->text('notes')->nullable()->comment('Note su modalità di invio documenti o scadenze fisse');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulatory_bodies');
    }
};
