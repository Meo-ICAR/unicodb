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
        Schema::table('audit_items', function (Blueprint $table) {
            $table->boolean('is_template')->default(false)->comment('Se è un elemento template riutilizzabile')->after('finding_description');
            $table->text('description')->nullable()->comment("Descrizione dettagliata dell'elemento");
            $table->string('audit_phase')->nullable()->comment("Fase dell'audit (es. preparazione, esecuzione, follow-up)");
            $table->string('code')->nullable()->comment("Codice identificativo dell'elemento");

            // Indici

            $table->index('is_template');
            $table->index('audit_phase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_items');
    }
};
