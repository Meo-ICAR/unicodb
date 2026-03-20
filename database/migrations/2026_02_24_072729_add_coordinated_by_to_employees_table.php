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
            // Aggiungo prima la colonna coordinated_by_id
            $table->unsignedInteger('coordinated_by_id')->nullable()->after('company_branch_id')->comment('ID del coordinatore (altro dipendente della stessa sede)');

            // Poi aggiungo la foreign key
            $table->foreign(['coordinated_by_id'], 'fk_employees_coordinated_by')->references(['id'])->on('employees')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('fk_employees_coordinated_by');
            $table->dropColumn('coordinated_by_id');
        });
    }
};
