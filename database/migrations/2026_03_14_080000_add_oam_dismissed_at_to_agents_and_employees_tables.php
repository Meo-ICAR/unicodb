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
        Schema::table('agents', function (Blueprint $table) {
            $table->date('oam_dismissed_at')->nullable()->comment('Data revoca OAM');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->date('oam_dismissed_at')->nullable()->comment('Data revoca OAM');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn('oam_dismissed_at');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('oam_dismissed_at');
        });
    }
};
