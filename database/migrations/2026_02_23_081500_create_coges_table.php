<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coges', function (Blueprint $table) {
            $table->comment('Piano dei conti e configurazioni per la contabilità generale');
            $table->id();
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade
            $table->string('fonte')->comment('Fonte del movimento contabile');
            $table->string('entrata_uscita')->comment('Entrata o Uscita');
            $table->string('conto_avere')->comment('Conto Avere');
            $table->string('descrizione_avere')->comment('Descrizione Conto Avere');
            $table->string('conto_dare')->comment('Conto Dare');
            $table->string('descrizione_dare')->comment('Descrizione Conto Dare');
            $table->string('annotazioni')->nullable()->comment('Note aggiuntive');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coges');
    }
};
