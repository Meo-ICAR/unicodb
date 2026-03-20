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
        Schema::table('documents', function (Blueprint $table) {
            // Campi per verifica documento - aggiungi solo se non esistono
            if (!Schema::hasColumn('documents', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->comment('Data e ora verifica documento');
            }
            if (!Schema::hasColumn('documents', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->comment('ID utente che ha verificato')->constrained('users')->onDelete('set null');
            }

            // Campi aggiuntivi per gestione completa - aggiungi solo se non esistono
            if (!Schema::hasColumn('documents', 'docnumber')) {
                $table->string('docnumber')->nullable()->comment('Numero protocollo documento');
            }
            if (!Schema::hasColumn('documents', 'emitted_by')) {
                $table->string('emitted_by')->nullable()->comment('Ente o autorità che ha rilasciato il documento');
            }
            if (!Schema::hasColumn('documents', 'emitted_at')) {
                $table->timestamp('emitted_at')->nullable()->comment('Data e ora di emissione documento');
            }
            if (!Schema::hasColumn('documents', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->comment('Data e ora di scadenza documento');
            }
            if (!Schema::hasColumn('documents', 'rejection_note')) {
                $table->text('rejection_note')->nullable()->comment('Note motivazione rifiuto documento');
            }

            // Index per performance - aggiungi solo se non esistono
            if (!Schema::hasIndex('documents', 'documents_verified_at_verified_by_index')) {
                $table->index(['verified_at', 'verified_by']);
            }
            if (!Schema::hasIndex('documents', 'documents_expires_at_index')) {
                $table->index(['expires_at']);
            }
            if (!Schema::hasIndex('documents', 'documents_emitted_at_index')) {
                $table->index(['emitted_at']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verified_at', 'verified_by', 'docnumber', 'emitted_by', 'emitted_at', 'expires_at', 'rejection_note']);
        });
    }
};
