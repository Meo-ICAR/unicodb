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
            $table->foreign(['company_id'], 'agents_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('no action');
            $table->foreign('company_branch_id')->references('id')->on('company_branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign('agents_ibfk_1');
            $table->dropForeign('agents_company_branch_id_foreign');
        });
    }
};
