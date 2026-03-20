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
        Schema::create('checklist_documents', function (Blueprint $table) {
            $table->id();
            // Il tipo di pratica (Mutuo, CQS, ecc.)
            $table->unsignedInteger('practice_scope_id')->constrained()->cascadeOnDelete();
            // Il tipo di documento richiesto
            $table->unsignedInteger('document_type_id')->constrained()->cascadeOnDelete();
            // La banca (opzionale: se null, vale per tutte le banche di quello scope)
            $table->unsignedInteger('principal_id')->nullable()->constrained()->cascadeOnDelete();

            $table->boolean('is_required')->default(true);
            $table->string('description')->nullable();  // Es: "Solo se coniugato"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_documents');
    }
};
