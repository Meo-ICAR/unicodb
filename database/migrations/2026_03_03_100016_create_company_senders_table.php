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
        Schema::create('company_senders', function (Blueprint $table) {
            $table->comment('Configurazione inviatori email per eventi aziendali');
            $table->id()->comment('ID univoco sender');
            $table->char('company_id', 36)->comment('ID azienda (multi-tenant)');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->string('eventgroup')->nullable()->comment('Evento aziendale');
            $table->string('eventcode')->nullable()->comment('Codice evento specifico');
            $table->string('emails')->nullable()->comment('Email a cui inviare per conoscenza (separate da virgola)');

            $table->string('name')->comment('Nome del sender/inviatore');
            $table->string('email')->comment('Email del sender');
            $table->boolean('is_active')->default(true)->comment('Sender attivo');
            $table->text('description')->nullable()->comment('Descrizione del sender');

            $table->timestamps();

            // Indici
            $table->index(['company_id', 'eventgroup', 'eventcode']);
            $table->index(['company_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_senders');
    }
};
