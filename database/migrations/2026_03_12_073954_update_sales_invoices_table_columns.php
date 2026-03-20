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
        Schema::table('sales_invoices', function (Blueprint $table) {
            // Add missing columns for sales invoices - only if they don't exist
            if (!Schema::hasColumn('sales_invoices', 'order_number')) {
                $table->string('order_number')->nullable()->after('number');
            }
            if (!Schema::hasColumn('sales_invoices', 'customer_number')) {
                $table->string('customer_number')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('sales_invoices', 'currency_code')) {
                $table->string('currency_code')->nullable()->after('customer_number');
            }
            if (!Schema::hasColumn('sales_invoices', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('amount_including_vat');
            }
            if (!Schema::hasColumn('sales_invoices', 'residual_amount')) {
                $table->decimal('residual_amount', 10, 2)->nullable()->after('amount');
            }
            if (!Schema::hasColumn('sales_invoices', 'ship_to_code')) {
                $table->string('ship_to_code')->nullable()->after('residual_amount');
            }
            if (!Schema::hasColumn('sales_invoices', 'ship_to_cap')) {
                $table->string('ship_to_cap')->nullable()->after('ship_to_code');
            }
            if (!Schema::hasColumn('sales_invoices', 'agent_code')) {
                $table->string('agent_code')->nullable()->after('ship_to_cap');
            }
            if (!Schema::hasColumn('sales_invoices', 'cdc_code')) {
                $table->string('cdc_code')->nullable()->after('agent_code');
            }
            if (!Schema::hasColumn('sales_invoices', 'dimensional_link_code')) {
                $table->string('dimensional_link_code')->nullable()->after('cdc_code');
            }
            if (!Schema::hasColumn('sales_invoices', 'location_code')) {
                $table->string('location_code')->nullable()->after('dimensional_link_code');
            }
            if (!Schema::hasColumn('sales_invoices', 'printed_copies')) {
                $table->integer('printed_copies')->default(0)->after('location_code');
            }
            if (!Schema::hasColumn('sales_invoices', 'payment_condition_code')) {
                $table->string('payment_condition_code')->nullable()->after('printed_copies');
            }
            if (!Schema::hasColumn('sales_invoices', 'closed')) {
                $table->boolean('closed')->default(false)->after('payment_condition_code');
            }
            if (!Schema::hasColumn('sales_invoices', 'cancelled')) {
                $table->boolean('cancelled')->default(false)->after('closed');
            }
            if (!Schema::hasColumn('sales_invoices', 'corrected')) {
                $table->boolean('corrected')->default(false)->after('cancelled');
            }
            if (!Schema::hasColumn('sales_invoices', 'email_sent')) {
                $table->boolean('email_sent')->default(false)->after('corrected');
            }
            if (!Schema::hasColumn('sales_invoices', 'email_sent_at')) {
                $table->timestamp('email_sent_at')->nullable()->after('email_sent');
            }
            if (!Schema::hasColumn('sales_invoices', 'bill_to_address')) {
                $table->string('bill_to_address')->nullable()->after('email_sent_at');
            }
            if (!Schema::hasColumn('sales_invoices', 'bill_to_city')) {
                $table->string('bill_to_city')->nullable()->after('bill_to_address');
            }
            if (!Schema::hasColumn('sales_invoices', 'bill_to_province')) {
                $table->string('bill_to_province')->nullable()->after('bill_to_city');
            }
            if (!Schema::hasColumn('sales_invoices', 'ship_to_address')) {
                $table->string('ship_to_address')->nullable()->after('bill_to_province');
            }
            if (!Schema::hasColumn('sales_invoices', 'ship_to_city')) {
                $table->string('ship_to_city')->nullable()->after('ship_to_address');
            }
            if (!Schema::hasColumn('sales_invoices', 'payment_method_code')) {
                $table->string('payment_method_code')->nullable()->after('ship_to_city');
            }
            if (!Schema::hasColumn('sales_invoices', 'customer_category')) {
                $table->string('customer_category')->nullable()->after('payment_method_code');
            }
            if (!Schema::hasColumn('sales_invoices', 'exchange_rate')) {
                $table->decimal('exchange_rate', 8, 4)->nullable()->after('customer_category');
            }
            if (!Schema::hasColumn('sales_invoices', 'bank_account')) {
                $table->string('bank_account')->nullable()->after('exchange_rate');
            }
            if (!Schema::hasColumn('sales_invoices', 'document_residual_amount')) {
                $table->decimal('document_residual_amount', 10, 2)->nullable()->after('bank_account');
            }
            if (!Schema::hasColumn('sales_invoices', 'document_type')) {
                $table->string('document_type')->nullable()->after('document_residual_amount');
            }
            if (!Schema::hasColumn('sales_invoices', 'credit_note_linked')) {
                $table->boolean('credit_note_linked')->default(false)->after('document_type');
            }
            if (!Schema::hasColumn('sales_invoices', 'in_order')) {
                $table->boolean('in_order')->default(false)->after('credit_note_linked');
            }
            if (!Schema::hasColumn('sales_invoices', 'supplier_description')) {
                $table->string('supplier_description')->nullable()->after('supplier_number');
            }
            if (!Schema::hasColumn('sales_invoices', 'purchase_invoice_origin')) {
                $table->string('purchase_invoice_origin')->nullable()->after('supplier_description');
            }
            if (!Schema::hasColumn('sales_invoices', 'sent_to_sdi')) {
                $table->boolean('sent_to_sdi')->default(false)->after('purchase_invoice_origin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
