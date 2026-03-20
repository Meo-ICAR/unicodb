<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('audit_items', function (Blueprint $table) {
            $table->comment('Singole verifiche effettuate durante un audit su specifiche pratiche o fascicoli agenti.');
            $table->increments('id')->comment('ID singola riga di controllo');
            $table->unsignedInteger('audit_id')->comment('Riferimento alla sessione di audit');
            $table->string('auditable_type')->comment('Classe dell\'oggetto controllato (es. App\\Models\\Practice)');
            $table->string('auditable_id', 36)->comment('ID dell\'oggetto controllato');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->enum('result', ['OK', 'RILIEVO', 'GRAVE_INADEMPIENZA', 'NON_CONTROLLATO'])->nullable()->default('OK');
            $table->text('finding_description')->nullable()->comment('Descrizione dell\'eventuale anomalia riscontrata');
            $table->text('remediation_plan')->nullable()->comment('Azioni correttive richieste per sanare l\'anomalia');
            $table->date('remediation_deadline')->nullable()->comment('Scadenza entro cui sanare il rilievo');
            $table->boolean('is_resolved')->nullable()->default(false)->comment('Indica se il rilievo è stato chiuso con successo');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_items');
    }
};
