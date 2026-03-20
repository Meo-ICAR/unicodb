<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('company_functions', function (Blueprint $table) {
            $table->comment('Organigramma aziendale e funzioni aziendali');
            $table->id()->comment('ID univoco funzione azienda');

            // Relazione con l'Azienda (es. il Mediatore Creditizio)
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->cascadeOnDelete();

            // Relazione con la Funzione (es. Compliance, AML, Direzione)
            $table
                ->foreignId('business_function_id')
                ->constrained('business_functions')
                ->onDelete('cascade');
            $table->string('code')->nullable()->comment('Codice identificativo funzione azienda');
            // Referente Interno (Dipendente/Esponente aziendale delegato al controllo)
            $table
                ->unsignedInteger('employee_id')
                ->nullable()
                ->comment('Referente interno');

            // Foreign key verso employees
            $table
                ->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('set null');

            // Referente Esterno / Outsourcer (usando la tua tabella clients)
            $table
                ->unsignedInteger('client_id')
                ->nullable()
                ->comment('Consulente esterno');

            // Foreign key verso clients
            $table
                ->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('set null');
            $table->date('contract_expiry_date')->nullable()->comment('Data scadenza contratto esternalizzazione');

            $table->boolean('is_privacy')->default(false)->comment('La funzione tratta dati personali ?');
            $table->boolean('is_outsourced')->default(false)->comment('La funzione è esternalizzata ?');
            $table->string('report_frequency')->nullable()->comment('Frequenza report (mensile, trimestrale, annuale)');

            $table->text('notes')->nullable()->comment('Note aggiuntive sulla funzione');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('company_function');
    }
};
