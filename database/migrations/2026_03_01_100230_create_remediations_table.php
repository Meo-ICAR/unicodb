<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('remediations', function (Blueprint $table) {
            $table->comment('Rimedi alle violazioni');
            $table->id();

            $table
                ->unsignedInteger('audit_item_id')
                ->nullable()
                ->comment('ID della riga audit di riferimento');

            // Foreign key verso audits
            $table
                ->foreign('audit_item_id')
                ->references('id')
                ->on('audit_items')
                ->nullable()
                ->onDelete('cascade');

            // Il nuovo campo enum per categorizzare il rimedio

            $table
                ->enum('remediation_type', [
                    'AML',
                    'Gestione Reclami',
                    'Monitoraggio Rete',
                    'Privacy',
                    'Trasparenza',
                    'Assetto Organizzativo'
                ])
                ->nullable()
                ->comment('categorizzare il rimedio');
            // La nuova foreign key verso la tabella functions (nullable)
            $table->string('name')->comment('nome rimedio');  // Ora conterrà solo il nome pulito dell'azione
            $table->string('code')->nullable()->comment('codice rimedio');
            $table->text('description')->nullable();
            $table
                ->foreignId('business_function_id')
                ->nullable()
                ->constrained('business_functions')
                ->nullOnDelete();  // Se elimino il reparto, il campo diventa null

            $table->integer('timeframe_hours')->nullable();
            $table->string('timeframe_desc')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('remediations');
    }
};
