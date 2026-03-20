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
        Schema::table('document_status', function (Blueprint $table) {
            $table->enum('status', [
                'ASSENTE',
                'DA VERIFICARE',
                'IN VERIFICA',
                'OK',
                'DIFFORME',
                'RICHIESTA INFO',
                'ERRATO',
                'ANNULLATO',
                'SCADUTO'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('document_status', function (Blueprint $table) {
            $table->enum('status', [
                'ASSENTE',
                'DA VERIFICARE',
                'IN VERIFICA',
                'OK',
                'DIFFORME',
                'RICHIESTA INFO',
                'ERRATO',
                'ANNULLATO'
            ])->change();
        });
    }
};
