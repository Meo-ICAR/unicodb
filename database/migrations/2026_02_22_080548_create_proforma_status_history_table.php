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
        Schema::create('proforma_status_history', function (Blueprint $table) {
            $table->comment('Registro storico dei passaggi di stato del proforma per controllo amministrativo.');
            $table->increments('id')->comment('ID univoco log stato');
            $table->unsignedInteger('proforma_id')->comment('Riferimento al proforma');
            $table->string('status', 50)->comment('Lo stato assunto dal proforma');
            $table->string('name')->nullable()->comment('Descrizione');
            $table->unsignedInteger('changed_by')->comment('L\'utente (amministratore) che ha effettuato l\'azione');
            $table->text('notes')->nullable()->comment('Eventuali note sul cambio stato (es. motivo dell\'annullamento)');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data e ora esatta del passaggio di stato');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_status_history');
    }
};
