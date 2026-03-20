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
        Schema::create('process_tasks', function (Blueprint $table) {
            $table->id();
            // Relazione con la tua tabella esistente practice_scopes
            $table
                ->unsignedInteger('practice_scope_id')
                ->constrained('practice_scopes')
                ->cascadeOnDelete();

            $table->string('name');  // Es: "Richiesta Allegato A", "Perizia Immobile"
            $table->string('slug');  // Es: "richiesta-allegato-a"
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Un task è unico all'interno dello stesso prodotto
            $table->unique(['practice_scope_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_tasks');
    }
};
