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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->nullable();  // Nr.
            $table->string('supplier_invoice_number')->nullable();  // Nr. fatt. fornitore
            $table->string('supplier_number')->nullable();  // Nr. fornitore
            $table->string('supplier')->nullable();  // Fornitore
            $table->string('currency_code')->nullable();  // Cod. valuta
            $table->decimal('amount', 10, 2)->nullable();  // Importo
            $table->decimal('amount_including_vat', 10, 2)->nullable();  // Importo IVA inclusa
            $table->string('pay_to_cap')->nullable();  // Pagare a - CAP
            $table->string('pay_to_country_code')->nullable();  // Pagare a - Cod. paese
            $table->date('registration_date')->nullable();  // Data di registrazione
            $table->string('location_code')->nullable();  // Cod. ubicazione
            $table->integer('printed_copies')->default(0);  // Copie stampate
            $table->date('document_date')->nullable();  // Data documento
            $table->string('payment_condition_code')->nullable();  // Cod. condizioni pagam.
            $table->date('due_date')->nullable();  // Data scadenza
            $table->string('payment_method_code')->nullable();  // Cod. metodo di pagamento
            $table->decimal('residual_amount', 10, 2)->nullable();  // Importo residuo
            $table->boolean('closed')->default(false);  // Chiuso
            $table->boolean('cancelled')->default(false);  // Annullato
            $table->boolean('corrected')->default(false);  // Rettifica
            $table->string('pay_to_address')->nullable();  // Pagare a - Indirizzo
            $table->string('pay_to_city')->nullable();  // Pagare a - Città
            $table->string('supplier_category')->nullable();  // Cat. reg. fornitore
            $table->decimal('exchange_rate', 10, 4)->nullable();  // Fattore valuta
            $table->string('vat_number')->nullable();  // Partita IVA
            $table->string('fiscal_code')->nullable();  // Codice fiscale
            $table->string('document_type')->nullable();  // Tipo Documento Fattura

            // Laravel standard fields
            $table->string('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->timestamps();

            // Indexes
            $table->index(['company_id', 'number']);
            $table->index(['company_id', 'supplier']);
            $table->index(['company_id', 'registration_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
