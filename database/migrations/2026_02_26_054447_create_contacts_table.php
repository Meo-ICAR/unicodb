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
        Schema::create('contacts', function (Blueprint $table) {
            $table->comment('Contatti aziendali es. sollecito pagamenti, referente');
            $table->id()->comment('ID univoco contatto');
            $table->foreignUuid('company_id')->nullable()->constrained('companies')->onDelete('cascade');

            // Relazione polimorfica - può essere Client, Principal, Agent
            $table->nullableMorphs('contactable');

            // Campi del referente
            $table->string('name')->comment('Nome e cognome del referente');
            $table->string('phone')->nullable()->comment('Numero di telefono');
            $table->string('email')->nullable()->comment('Indirizzo email');
            $table->string('role_type')->nullable()->comment('Ruolo o tipo di referente');
            $table->text('description')->nullable()->comment('Note o descrizione aggiuntiva');

            $table->timestamps();

            // Indici
            $table->index(['company_id', 'contactable_type', 'contactable_id']);
            $table->index(['name']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
