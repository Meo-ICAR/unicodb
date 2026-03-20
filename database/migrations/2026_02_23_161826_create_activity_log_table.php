<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        Schema::connection(config('activitylog.database_connection'))->create(config('activitylog.table_name'), function (Blueprint $table) {
            $table->comment('Activity logging for Laravel Spatie ActivityLog package.');
            $table->bigIncrements('id')->comment('ID univoco del log attività');
            $table->string('log_name')->nullable()->comment('Nome del log per raggruppamento attività');
            $table->text('description')->comment("Descrizione testuale dell'attività");
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->json('properties')->nullable()->comment('Proprietà aggiuntive in formato JSON');
            $table->timestamps();
            $table->index('log_name');

            $table->unsignedInteger('practice_id')->comment('Riferimento alla pratica')->nullable();

            $table->unsignedInteger('client_id')->comment('Riferimento al cliente tramite il mandato')->nullable();

            // Dati dell'evento intercettato
            $table->enum('tipo_evento', ['instaurazione_rapporto', 'esecuzione_operazione', 'chiusura_rapporto'])->nullable()->comment('Tipo di evento AUI registrato');
            $table->date('data_evento')->nullable()->comment("Data di occorrenza dell'evento");
            $table->decimal('importo_rilevato', 15, 2)->nullable()->comment("Importo finanziario rilevato dall'evento");

            // Fotografia dei dati in formato JSON per averli pronti
            $table->json('payload_dati_cliente')->nullable()->comment('Snapshot dati cliente in formato JSON');

            // Stato di consolidamento
            $table->enum('stato', ['da_consolidare', 'consolidato', 'scartato'])->default('da_consolidare')->comment('Stato di elaborazione del log');
            $table->text('note_operatore')->nullable()->comment("Note interne dell'operatore");

            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();
            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade
        });
    }

    public function down()
    {
        Schema::connection(config('activitylog.database_connection'))->dropIfExists(config('activitylog.table_name'));
    }
}
