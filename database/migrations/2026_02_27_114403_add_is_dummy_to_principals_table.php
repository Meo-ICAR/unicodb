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
        Schema::table('principals', function (Blueprint $table) {
            $table
                ->boolean('is_dummy')
                ->default(false)
                ->after('status')
                ->comment('Mandante fittizio / non convenzionato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('principals', function (Blueprint $table) {
            $table->dropColumn('is_dummy');
        });
    }
};
