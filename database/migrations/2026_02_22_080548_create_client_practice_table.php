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
        Schema::create('client_practice', function (Blueprint $table) {
            $table->comment('Tabella di legame tra Clienti e Pratiche. Gestisce chi sono gli intestatari e chi i garanti per ogni pratica.');
            $table->increments('id')->comment('ID univoco del legame');
            $table->unsignedInteger('practice_id')->comment('Riferimento alla pratica');
            $table->unsignedInteger('client_id')->comment('Riferimento al cliente coinvolto');
            $table->enum('role', ['intestatario', 'cointestatario', 'garante', 'terzo_datore'])->default('intestatario')->comment('Ruolo legale del cliente nella pratica: Intestatario principale, Co-intestatario, Garante o Terzo Datore di ipoteca');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->text('notes')->nullable()->comment('Note specifiche sul ruolo per questa pratica (es. "Garante solo per quota 50%")');
            // --- CAMPI COMPLIANCE SPECIFICI PER QUESTA PERSONA IN QUESTA PRATICA ---
            // Obblighi AML: Scopo e Natura
            $table->text('purpose_of_relationship')->nullable()->comment('Es: Acquisto prima casa');

            $table->text('funds_origin')->nullable()->comment('Es: Risparmi, donazione, stipendio');

            // 1. Trasparenza OAM
            $table->boolean('oam_delivered')->default(false)->comment('Foglio informativo consegnato a questo soggetto?');

            // 3. Rischio specifico per il ruolo (Il garante potrebbe avere rischio basso, il richiedente alto)
            $table->enum('role_risk_level', ['basso', 'medio', 'alto'])->nullable()->comment('Livello rischio specifico ruolo nella pratica');

            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            $table->timestamps();

            $table->unique(['practice_id', 'client_id'], 'unique_client_practice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_practice');
    }
};
