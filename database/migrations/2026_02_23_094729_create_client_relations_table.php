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
        Schema::create('client_relations', function (Blueprint $table) {
            $table->comment('Composizione societaria');
            $table->id()->comment('ID univoco relazione cliente');

            // La società (persona giuridica) - DEVE puntare a companies table
            // foreignId crea un BIGINT UNSIGNED compatibile con l'ID di default di Laravel
            // Deve essere char(36) per matchare companies.id
            $table->char('company_id', 36)->comment('ID società persona giuridica');
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

            // Deve essere unsignedInteger per matchare clients.id
            $table->unsignedInteger('client_id')->comment('ID persona fisica cliente');
            $table
                ->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->onDelete('cascade');
            // Il ruolo (socio, amministratore, legale rappresentante)
            $table->decimal('shares_percentage', 5, 2)->nullable()->comment('Percentuale quote possedute');  // Opzionale per le quote
            $table->boolean('is_titolare')->default(false)->comment('Se è titolare/socio di maggioranza');
            $table->unsignedInteger('client_type_id')->nullable()->comment('Tipo di cliente');
            $table->foreign('client_type_id')->references('id')->on('client_types')->onDelete('cascade');
            $table->date('data_inizio_ruolo')->nullable()->comment('Data inizio ruolo');
            $table->date('data_fine_ruolo')->nullable()->comment('Data fine ruolo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_relations');
    }
};
