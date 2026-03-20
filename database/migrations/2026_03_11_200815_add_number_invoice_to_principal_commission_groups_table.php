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
        Schema::table('principal_commission_groups', function (Blueprint $table) {
            $table->string('number_invoice')->nullable()->after('sales_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('principal_commission_groups', function (Blueprint $table) {
            $table->dropColumn('number_invoice');
        });
    }
};
