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
        Schema::create('checklists', function (Blueprint $table) {
            $table->comment('Checklist per workflow con domande e allegati');
            $table->id()->comment('ID univoco checklist');
            $table->char('company_id', 36)->nullable()->comment('Agenzia proprietaria (multi-tenant)');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('name')->comment('Nome della checklist')->nullable();
            $table->string('code')->comment('Codice della checklist')->nullable();
            $table->enum('type', ['loan_management', 'audit'])->comment('Tipo di checklist')->nullable();
            $table->text('description')->nullable()->comment('Descrizione della checklist');
            $table->unsignedInteger('principal_id')->nullable()->comment('Principal specifico (se applicabile)');
            $table->boolean('is_practice')->default(false)->comment('Se riferisce a pratiche')->nullable();
            $table->boolean('is_audit')->default(false)->comment('Se per audit/compliance')->nullable();

            $table->boolean('is_template')->default(true)->comment('Se è un template riutilizzabile')->nullable();

            // Relazione Polimorfica: a chi appartiene questa specifica copia?
            // target_type sarà es. 'App\Models\Agent' o 'App\Models\Pratica'
            // target_id sarà l'ID dell'agente o della pratica
            $table->nullableMorphs('target');

            // Documenti associati
            $table->char('document_id', 36)->nullable()->comment('Documento operativo di company');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('set null');

            // Stato di completamento
            $table->enum('status', ['da_compilare', 'in_corso', 'completata'])->default('da_compilare')->comment('Stato checklist')->nullable();

            $table->timestamps();
            // Indici

            $table->index(['company_id', 'type']);
            $table->index('principal_id');

            // Foreign keys

            $table->foreign('principal_id')->references('id')->on('principals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklists');
    }
};
