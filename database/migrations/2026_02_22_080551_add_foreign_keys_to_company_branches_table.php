<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('company_branches', function (Blueprint $table) {
            $table->foreign(['company_id'], 'company_branches_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_branches', function (Blueprint $table) {
            $table->dropForeign('company_branches_ibfk_1');
        });
    }
};
