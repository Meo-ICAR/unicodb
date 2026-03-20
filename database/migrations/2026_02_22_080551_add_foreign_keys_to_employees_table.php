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
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign(['company_id'], 'employees_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('cascade');

            $table->foreign(['company_branch_id'], 'employees_ibfk_3')->references(['id'])->on('company_branches')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_ibfk_1');
            $table->dropForeign('employees_ibfk_2');
            $table->dropForeign('employees_ibfk_3');
        });
    }
};
