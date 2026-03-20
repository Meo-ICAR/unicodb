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
        Schema::table('websites', function (Blueprint $table) {
            $table->string('websiteable_type')->nullable()->default(null)->change();
            $table->char('websiteable_id', 36)->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->string('websiteable_type')->nullable()->default(null)->change();
            $table->char('websiteable_id', 36)->nullable()->default(null)->change();
        });
    }
};
