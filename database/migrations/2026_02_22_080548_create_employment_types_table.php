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
        Schema::create('employment_types', function (Blueprint $table) {
            $table->comment('Catalogo globale: Classificazione lavorativa del cliente (fondamentale per le logiche di delibera del credito).');
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->string('name')->comment('Descrizione');
            $table->unsignedInteger('company_type_id')->nullable()->comment('Ruolo specifico per quella determinata categoria di company');
            $table->foreign('company_type_id')->references('id')->on('company_types')->onDelete('set null');

            $table->unsignedInteger('client_type_id')->nullable()->comment('Ruolo specifico per quella determinata categoria di clienti');
            $table->string('privacy_role')->nullable()->comment('Ruolo Privacy (es. Titolare Autonomo, Responsabile Esterno)');

            $table->text('purpose')->nullable()->comment('Finalità del trattamento');
            $table->text('data_subjects')->nullable()->comment('Categorie di Interessati');
            $table->text('data_categories')->nullable()->comment('Categorie di Dati Trattati');
            $table->string('retention_period')->nullable()->comment('Tempi di Conservazione (Data Retention)');
            $table->string('extra_eu_transfer')->nullable()->comment('Trasferimento Extra-UE');
            $table->text('security_measures')->nullable()->comment('Misure di Sicurezza');
            $table->string('privacy_data')->nullable()->comment('Altri Dati Privacy');

            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data di creazione');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Ultima modifica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employment_types');
    }
};
