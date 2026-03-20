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
            $table->foreign(['company_id'], 'practice_commissions_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['practice_id'], 'practice_commissions_ibfk_2')->references(['id'])->on('practices')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['proforma_id'], 'practice_commissions_ibfk_3')->references(['id'])->on('proformas')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practice_commissions', function (Blueprint $table) {
            $table->dropForeign('practice_commissions_ibfk_1');
            $table->dropForeign('practice_commissions_ibfk_2');
            $table->dropForeign('practice_commissions_ibfk_3');
            $table->dropForeign('practice_commissions_ibfk_4');
        });
    }
};
