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
        Schema::table('rui_cariche', function (Blueprint $table) {
            $table->string('pf_name')->nullable()->comment('Nome persona fisica');
            $table->string('pg_name')->nullable()->comment('Nome persona giuridica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rui_cariche', function (Blueprint $table) {
            $table->dropColumn(['pf_name', 'pg_name']);
        });
    }
};
