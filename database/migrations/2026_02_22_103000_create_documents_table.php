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
        Schema::create('documents', function (Blueprint $table) {
            $table->comment('Documentazione');
            $table->char('id', 36)->primary()->comment('UUID del documento');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade

            // Campi polymorphic per documentable
            $table->string('documentable_type', 255)->comment('Tipo di modello associato (es. Client, Employee, Practice)');
            $table->char('documentable_id', 36)->comment('ID del modello associato');

            $table->unsignedInteger('document_type_id')->nullable()->comment('ID del tipo di documento associato');
            $table->string('name')->nullable()->comment('Nome del documento');
            $table->string('emitted_by')->nullable()->comment('Ente di rilascio');
            $table->string('status')->default('uploaded')->comment('Stato del documento');
            $table->boolean('is_template')->default(false)->comment('Indica se forniamo noi il documento');
            $table->date('expires_at')->nullable()->comment('Scadenza documento');
            $table->date('emitted_at')->nullable()->comment('Data emissione documento');
            $table->string('docnumber')->nullable()->comment('Numero documento');
            $table->timestamp('delivered_at')->nullable()->comment('Documento consegnato il');
            $table->timestamp('signed_at')->nullable()->comment('Firmato il');
            // Campi audit aggiunti
            $table->text('rejection_note')->nullable()->comment('Motivazione in caso di documento rifiutato');
            $table->timestamp('verified_at')->nullable()->comment('Data e ora della verifica');
            $table
                ->foreignId('verified_by')
                ->nullable()
                ->comment("ID dell'utente/admin che ha verificato il documento")
                ->constrained('users')  // Indica esplicitamente la tabella users
                ->onDelete('set null');

            $table
                ->foreignId('uploaded_by')
                ->nullable()
                ->comment("ID dell'utente/admin che ha caricato il documento")
                ->constrained('users')  // Indica esplicitamente la tabella users
                ->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indici
            $table->index(['documentable_type', 'documentable_id']);

            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
