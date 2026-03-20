<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->comment('Queue jobs for Laravel task processing.');
            $table->id()->comment('ID univoco job queue');
            $table->string('queue')->index()->comment('Nome queue');
            $table->longText('payload')->comment('Dati serializzati job');
            $table->unsignedTinyInteger('attempts')->comment('Numero tentativi esecuzione');
            $table->unsignedInteger('reserved_at')->nullable()->comment('Timestamp prenotazione');
            $table->unsignedInteger('available_at')->comment('Timestamp disponibilità esecuzione');
            $table->unsignedInteger('created_at')->comment('Timestamp creazione job');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->comment('Batch tracking for Laravel queue jobs.');
            $table->string('id')->primary()->comment('ID univoco batch');
            $table->string('name')->comment('Nome batch');
            $table->integer('total_jobs')->comment('Numero totale job nel batch');
            $table->integer('pending_jobs')->comment('Numero job in attesa');
            $table->integer('failed_jobs')->comment('Numero job falliti');
            $table->longText('failed_job_ids')->comment('ID job falliti serializzati');
            $table->mediumText('options')->nullable()->comment('Opzioni batch serializzate');
            $table->integer('cancelled_at')->nullable()->comment('Timestamp cancellazione');
            $table->integer('created_at')->comment('Timestamp creazione batch');
            $table->integer('finished_at')->nullable()->comment('Timestamp completamento batch');
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->comment('Failed queue jobs for Laravel debugging and retry.');
            $table->id()->comment('ID univoco job fallito');
            $table->string('uuid')->unique()->comment('UUID univoco job fallito');
            $table->text('connection')->comment('Nome connessione queue');
            $table->text('queue')->comment('Nome queue');
            $table->longText('payload')->comment('Dati serializzati job');
            $table->longText('exception')->comment('Eccezione completa');
            $table->timestamp('failed_at')->useCurrent()->comment('Timestamp fallimento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
}
