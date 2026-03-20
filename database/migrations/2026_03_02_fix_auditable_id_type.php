<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Modifico il campo da unsignedInteger a string per supportare UUID
            $table->string('auditable_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            // Torno indietro a unsignedInteger
            $table->unsignedInteger('auditable_id')->nullable()->change();
        });
    }
};
