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
        Schema::table('audits', function (Blueprint $table) {
            $table->unsignedInteger('regulatory_body_id')->nullable()->comment("Ente regolatore che richiede l'audit (se applicabile)");
            $table->unsignedInteger('client_id')->nullable()->comment('Cliente specifico oggetto di audit (se applicabile)');

            // Aggiungo gli indici
            $table->index('regulatory_body_id', 'regulatory_body_id');
            $table->index('client_id', 'client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropIndex('regulatory_body_id');
            $table->dropIndex('client_id');
            $table->dropColumn(['regulatory_body_id', 'client_id']);
        });
    }
};
