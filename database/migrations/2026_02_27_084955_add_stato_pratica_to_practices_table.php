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
        Schema::table('practices', function (Blueprint $table) {
            $table
                ->string('stato_pratica')
                ->nullable()
                ->after('practice_status_id')
                ->comment('Stato pratica originale da sistema esterno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropColumn('stato_pratica');
        });
    }
};
