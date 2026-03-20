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
        Schema::create('agent_commission_groups', function (Blueprint $table) {
            $table->id();
            $table->string('company_id');
            $table->unsignedInteger('agent_id');
            $table->date('invoice_at');
            $table->decimal('total_commission_amount', 10, 2);
            $table->decimal('total_invoice_amount', 10, 2)->nullable();
            $table->decimal('commission_percentage', 5, 2)->nullable();
            $table->unsignedBigInteger('purchase_invoice_id')->nullable();
            $table->boolean('is_matched')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('cascade');
            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices')->onDelete('set null');

            // Indexes for performance
            $table->index(['company_id', 'agent_id', 'invoice_at']);
            $table->index(['company_id', 'is_matched']);
            $table->index(['purchase_invoice_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_commission_groups');
    }
};
