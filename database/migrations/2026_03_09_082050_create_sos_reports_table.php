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
        Schema::create('sos_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aui_record_id')->nullable()->constrained('aui_records');

            $table->unsignedInteger('client_mandate_id')->nullable();  // FK alla tua tabella mandati dei clienti
            $table->char('company_id', 36);

            $table->string('codice_protocollo_interno')->unique();  // Es: SOS-2026-001
            $table->enum('stato', ['istruttoria', 'archiviata', 'segnalata_uif'])->default('istruttoria');
            $table->enum('grado_sospetto', ['basso', 'medio', 'alto'])->default('basso');

            $table->text('motivo_sospetto');  // Descrizione dell'anomalia riscontrata
            $table->text('decisione_finali')->nullable();  // Note del Responsabile AML
            $table->date('data_segnalazione_uif')->nullable();
            $table->string('protocollo_uif')->nullable();  // Riferimento portale INFOSTAT/UIF

            $table->foreignId('responsabile_id')->nullable()->constrained('users');  // Chi ha gestito la pratica
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sos_reports');
    }
};
