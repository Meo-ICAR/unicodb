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
            $table
                ->unsignedTinyInteger('practice_commission_status_id')
                ->nullable()
                ->after('proforma_id')
                ->comment('ID stato commissione pratica');

            $table
                ->foreign('practice_commission_status_id')
                ->references('id')
                ->on('practice_commission_statuses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_commissions', function (Blueprint $table) {
            $table->dropForeign('practice_commissions_practice_commission_status_id_foreign');
            $table->dropColumn('practice_commission_status_id');
        });
    }
};
