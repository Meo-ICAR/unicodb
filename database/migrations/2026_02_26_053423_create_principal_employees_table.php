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
        Schema::create('principal_employees', function (Blueprint $table) {
            $table->comment('Dipendenti autorizzati a lavorare con mandante');
            $table->id();
            $table->unsignedInteger('principal_id')->constrained('principals')->onDelete('cascade');
            $table->string('usercode')->unique()->comment('Codice identificativo utente sul portale')->nullable();
            $table->string('description')->nullable()->comment('Descrizione ruolo o note');
            $table->date('start_date')->comment('Data inizio autorizzazione')->nullable();
            $table->date('end_date')->nullable()->comment('Data fine autorizzazione');
            $table->boolean('is_active')->default(true)->comment('Stato attivo/inattivo');
            $table->timestamps();

            // Indici
            $table->index(['principal_id', 'is_active']);
            $table->index(['usercode']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('principal_employees');
    }
};
