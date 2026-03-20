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
        Schema::table('proforma_status_history', function (Blueprint $table) {
            $table->foreign(['proforma_id'], 'proforma_status_history_ibfk_1')->references(['id'])->on('proformas')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proforma_status_history', function (Blueprint $table) {
            $table->dropForeign('proforma_status_history_ibfk_1');
            $table->dropForeign('proforma_status_history_ibfk_2');
        });
    }
};
