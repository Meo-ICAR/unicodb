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
        Schema::create('software_mappings', function (Blueprint $table) {
            $table->comment('Tabelle di conversione (Cross-Reference) per tradurre i dati da software esterni al formato interno.');
            $table->increments('id')->comment('ID univoco mappatura');
            $table->unsignedInteger('software_application_id')->comment('Il software sorgente (es. CRM esterno)');
            $table->enum('mapping_type', ['PRACTICE_TYPE', 'PRACTICE_STATUS', 'CLIENT_TYPE', 'BANK_NAME', 'COMMISSION_STATUS'])->comment('Cosa stiamo mappando (es. Tipo Pratica o Stato Pratica)');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->string('code')->comment('Nostro codice alfanumerico (es. "MUT_ACQ")')->nullable();
            $table->string('external_value')->comment('Il valore testuale nel CRM sorgente (es. "Mutuo immobiliare")')->nullable();
            $table->unsignedInteger('internal_id')->comment('L\'ID corrispondente nel nostro database (es. ID di "Mutuo Acquisto")')->nullable();
            $table->string('description')->nullable()->comment('Descrizione');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();

            $table->index(['software_application_id', 'mapping_type', 'external_value'], 'idx_mapping_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_mappings');
    }
};
