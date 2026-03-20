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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('company_id');
            $table->string('number');  // Nr.
            $table->string('order_number')->nullable();  // Nr. ordine
            $table->string('customer_number');  // Nr. cliente
            $table->string('customer_name');  // Ragione Sociale
            $table->string('currency_code')->nullable();  // Cod. valuta
            $table->date('due_date')->nullable();  // Data scadenza
            $table->decimal('amount', 10, 2);  // Importo
            $table->decimal('amount_including_vat', 10, 2);  // Importo IVA inclusa
            $table->decimal('residual_amount', 10, 2);  // Importo residuo
            $table->string('ship_to_code')->nullable();  // Spedire a - Codice
            $table->string('ship_to_cap')->nullable();  // Spedire a - CAP
            $table->date('registration_date');  // Data di registrazione
            $table->string('agent_code')->nullable();  // Cod. agente
            $table->string('cdc_code')->nullable();  // Cdc Codice
            $table->string('dimensional_link_code')->nullable();  // Cod. colleg. dimen. 2
            $table->string('location_code')->nullable();  // Cod. ubicazione
            $table->integer('printed_copies')->default(0);  // Copie stampate
            $table->string('payment_condition_code')->nullable();  // Cod. condizioni pagam.
            $table->boolean('closed')->default(false);  // Chiuso
            $table->boolean('cancelled')->default(false);  // Annullato
            $table->boolean('corrected')->default(false);  // Rettifica
            $table->boolean('email_sent')->default(false);  // E-mail inviata
            $table->dateTime('email_sent_at')->nullable();  // Data/ora invio mail
            $table->text('bill_to_address')->nullable();  // Fatturare a - Indirizzo
            $table->text('bill_to_city')->nullable();  // Fatturare a - Città
            $table->string('bill_to_province')->nullable();  // Provincia di fatturazione
            $table->text('ship_to_address')->nullable();  // Spedire a - Indirizzo
            $table->text('ship_to_city')->nullable();  // Spedire a - Città
            $table->string('payment_method_code')->nullable();  // Cod. metodo di pagamento
            $table->string('customer_category')->nullable();  // Cat. reg. cliente
            $table->decimal('exchange_rate', 10, 2)->nullable();  // Fattore valuta
            $table->string('vat_number')->nullable();  // Partita IVA
            $table->string('bank_account')->nullable();  // C/C bancario
            $table->decimal('document_residual_amount', 10, 2)->nullable();  // Importo residuo documento
            $table->string('document_type');  // Tipo di documento Fattura
            $table->string('credit_note_linked')->nullable();  // Nota di Credito Collegata
            $table->boolean('in_order')->default(false);  // Flg In Commessa
            $table->string('supplier_number')->nullable();  // Nr. fornitore
            $table->text('supplier_description')->nullable();  // Descrizione Fornitore
            $table->string('purchase_invoice_origin')->nullable();  // Fattura Acquisto Origine
            $table->boolean('sent_to_sdi')->default(false);  // Inviato allo SDI
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Indexes for performance
            $table->index(['company_id', 'number']);
            $table->index(['company_id', 'customer_number']);
            $table->index(['company_id', 'registration_date']);
            $table->index(['company_id', 'agent_code']);
            $table->index(['company_id', 'document_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
