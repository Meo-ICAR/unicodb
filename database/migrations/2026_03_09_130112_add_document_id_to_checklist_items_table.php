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
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->boolean('is_completed')->default(false)->comment('Step completato');
            // 4. SINCRONIZZAZIONE BIDIREZIONALE (Dal Model alla Checklist)
            $table->string('target_model')->nullable()->comment('Modello del parent da ascoltare (es. Agent, Company)');
            $table->string('target_field')->nullable()->comment('Colonna del parent da ascoltare (es. status, privacy_signed_at)');
            $table->string('target_value')->nullable()->comment('Valore esatto che fa scattare la spunta (es. deliberata)');
            $table->boolean('is_timestamp_update')->default(false)->comment('Se true, basta che il target_field non sia null per spuntare');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropForeign(['document_id']);
            $table->dropColumn('document_id');
        });
    }
};
