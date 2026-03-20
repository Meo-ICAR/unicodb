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
        Schema::table('document_types', function (Blueprint $table) {
            // Assicura che i campi esistano e siano configurati correttamente
            if (!Schema::hasColumn('document_types', 'regex')) {
                $table->string('regex')->nullable()->comment('Pattern regex per classificazione automatica documenti');
            }

            if (!Schema::hasColumn('document_types', 'duration')) {
                $table->integer('duration')->nullable()->comment('Validità documento in giorni dal rilascio');
            }
            if (!Schema::hasColumn('document_types', 'is_endmonth')) {
                $table->boolean('is_endmonth')->nullable()->default(false)->comment('Approssima data a fine mese');
            }
            if (!Schema::hasColumn('document_types', 'priority')) {
                $table->integer('priority')->default(0)->comment('Priorità per esecuzione regex (valori più alti prima)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            // Rinomina indietro da regex a regex_pattern
            $table->renameColumn('regex', 'regex_pattern');
        });
    }
};
