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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->string('invoiceable_type')->nullable()->after('company_id')->comment('Type of model (Client, Agent, etc.)');
            $table->char('invoiceable_id', 36)->nullable()->after('invoiceable_type')->comment('ID of the related model');

            $table->index(['invoiceable_type', 'invoiceable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropIndex(['invoiceable_type', 'invoiceable_id']);
            $table->dropColumn(['invoiceable_type', 'invoiceable_id']);
        });
    }
};
