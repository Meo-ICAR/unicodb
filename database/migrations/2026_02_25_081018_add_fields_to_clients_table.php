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
        Schema::table('clients', function (Blueprint $table) {
            // Campo per i subfornitori da comunicare per gradimento
            $table->text('subfornitori')->nullable()->comment('Subfornitori da comunicare per gradimento');

            // Campi per approvazione e blacklist
            $table->boolean('is_requiredApprovation')->default(false)->comment('Da far approvare per gradimento');
            $table->boolean('is_approved')->default(true)->comment('Approvata per gradimento');
            $table->boolean('is_anonymous')->default(false)->comment('Cliente anonimo (non comunicabile)');
            $table->timestamp('blacklist_at')->nullable()->comment('Data inserimento in blacklist');
            $table->string('blacklisted_by')->nullable()->comment("ID dell'utente che ha inserito in blacklist (senza link esterni)");

            // Campi per retribuzione
            $table->decimal('salary', 10, 2)->nullable()->comment('Retribuzione annuale del cliente');
            $table->decimal('salary_quote', 10, 2)->nullable()->comment('Quota retribuzione per calcoli finanziari');

            // Indici per performance
            $table->index('blacklist_at');
            $table->index('is_anonymous');
            $table->index('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['subfornitori', 'is_approved', 'is_anonymous', 'blacklist_at', 'blacklisted_by', 'salary', 'salary_quote']);
        });
    }
};
