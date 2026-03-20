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
        Schema::table('rui_collaboratori', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('rui_collaboratori', 'intermediario')) {
                $table->string('intermediario')->nullable()->after('qualifica_rapporto');
            }
            if (!Schema::hasColumn('rui_collaboratori', 'collaboratore')) {
                $table->string('collaboratore')->nullable()->after('intermediario');
            }
            if (!Schema::hasColumn('rui_collaboratori', 'dipendente')) {
                $table->string('dipendente')->nullable()->after('collaboratore');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rui_collaboratori', function (Blueprint $table) {
            $table->dropColumn(['intermediario', 'collaboratore', 'dipendente']);
        });
    }
};
