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
        Schema::table('practice_commissions', function (Blueprint $table) {
            $table->string('alternative_number_invoice')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_commissions', function (Blueprint $table) {
            $table->dropColumn('alternative_number_invoice');
        });
    }
};
