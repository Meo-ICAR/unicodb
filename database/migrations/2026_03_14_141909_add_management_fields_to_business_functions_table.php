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
        Schema::table('business_functions', function (Blueprint $table) {
            $table->string('managed_by_code')->nullable()->comment('Code of the function that manages it');
            $table->longText('mission')->nullable()->comment('What does the function do');
            $table->longText('responsibility')->nullable()->comment('List of activities and responsibilities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_functions', function (Blueprint $table) {
            $table->dropColumn(['managed_by_code', 'mission', 'responsibility']);
        });
    }
};
