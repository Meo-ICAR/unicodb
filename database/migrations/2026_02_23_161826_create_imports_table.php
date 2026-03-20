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
        Schema::create('imports', function (Blueprint $table): void {
            $table->comment('Log Importazioni dati');
            $table->id()->comment('ID univoco importazione');
            $table->timestamp('completed_at')->nullable()->comment('Data e ora completamento importazione');
            $table->string('file_name')->comment('Nome file importato');
            $table->string('file_path')->comment('Percorso completo file importato');
            $table->string('importer')->comment('Classe/entità che gestisce importazione');
            $table->unsignedInteger('processed_rows')->default(0)->comment('Numero righe processate');
            $table->unsignedInteger('total_rows')->comment('Numero totale righe da importare');
            $table->unsignedInteger('successful_rows')->default(0)->comment('Numero righe importate con successo');
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
        Schema::dropIfExists('imports');
    }
};
