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
        Schema::create('principal_commission_groups', function (Blueprint $table) {
            $table->id();
            $table->string('company_id');
            $table->unsignedInteger('principal_id');
            $table->date('invoice_at');
            $table->decimal('total_commission_amount', 10, 2);
            $table->decimal('total_invoice_amount', 10, 2)->nullable();
            $table->decimal('commission_percentage', 8, 2)->nullable();
            $table->unsignedBigInteger('sales_invoice_id')->nullable();
            $table->boolean('is_matched')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('set null');

            // Indexes for performance
            $table->index(['company_id', 'principal_id', 'invoice_at'], 'pcg_principal_date_idx');
            $table->index(['company_id', 'is_matched'], 'pcg_matched_idx');
            $table->index(['sales_invoice_id'], 'pcg_sales_invoice_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('principal_commission_groups');
    }
};
