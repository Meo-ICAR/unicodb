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
            $table->boolean('is_company')->default(false)->after('status')->comment("True se il cliente è un'azienda fornitore");
            $table->boolean('is_lead')->default(false)->after('is_company')->comment('True se è un lead non ancora convertito');
            $table->unsignedInteger('leadsource_id')->nullable()->after('is_lead')->comment('ID del client che ha fornito il lead');
            $table->timestamp('acquired_at')->nullable()->after('leadsource_id')->comment('Data di acquisizione del contatto');
            $table->foreign(['leadsource_id'], 'clients_ibfk_4')->references(['id'])->on('clients')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('clients_ibfk_4');
            $table->dropColumn(['is_company', 'is_lead', 'leadsource_id', 'acquired_at']);
        });
    }
};
