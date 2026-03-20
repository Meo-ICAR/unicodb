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
        Schema::create('exports', function (Blueprint $table): void {
            $table->comment('Log Esportazioni dati');
            $table->id()->comment('ID univoco esportazione');
            $table->timestamp('completed_at')->nullable()->comment('Data e ora completamento esportazione');
            $table->string('file_disk')->comment('Storage disk dove salvare file');
            $table->string('file_name')->nullable()->comment('Nome file esportato');
            $table->string('exporter')->comment('Classe/entità che gestisce esportazione');
            $table->unsignedInteger('processed_rows')->default(0)->comment('Numero righe processate');
            $table->unsignedInteger('total_rows')->comment('Numero totale righe da esportare');
            $table->unsignedInteger('successful_rows')->default(0)->comment('Numero righe esportate con successo');
            // Chi ha causato l'anomalia? (Può essere null se è un attacco esterno)
            $table
                ->foreignId('user_id')
                ->nullable()
                ->comment("ID dell'utente collegato")
                ->constrained('users')  // Indica esplicitamente la tabella users
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exports');
    }
};
