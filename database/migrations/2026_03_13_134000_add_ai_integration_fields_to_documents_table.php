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
        Schema::table('documents', function (Blueprint $table) {
            // 1. Aggiunta campi per l'AI
            $table->text('ai_abstract')->nullable()->comment('Riassunto generato dall AI')->after('description');
            $table->tinyInteger('ai_confidence_score')->unsigned()->nullable()->comment('Affidabilità classificazione AI (0-100)')->after('ai_abstract');
            $table->longText('extracted_text')->nullable()->comment('Testo grezzo estratto dal PDF')->after('ai_confidence_score');
            $table->json('metadata')->nullable()->comment('Dati chiave estratti dall AI in formato JSON')->after('extracted_text');
            
            // 2. Aggiunta campi per l'integrazione e sicurezza
            $table->string('sharepoint_id', 255)->nullable()->comment('ID univoco del nodo SharePoint')->after('id');
            $table->string('file_hash', 64)->nullable()->comment('SHA-256 del file per prevenire duplicati')->after('sharepoint_id');
            
            // 3. Indici univoci per integrazione e sicurezza
            $table->unique(['sharepoint_id'])->name('documents_sharepoint_id_unique');
            $table->unique(['file_hash'])->name('documents_file_hash_unique');
            
            // 4. Rimozione campo ridondante (se presente)
            if (Schema::hasColumn('documents', 'document_status_id')) {
                $table->dropColumn('document_status_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropUnique('documents_sharepoint_id_unique');
            $table->dropUnique('documents_file_hash_unique');
            $table->dropColumn(['ai_abstract', 'ai_confidence_score', 'extracted_text', 'metadata', 'sharepoint_id', 'file_hash']);
            
            // Ripristina campo rimosso se necessario
            if (!Schema::hasColumn('documents', 'document_status_id')) {
                $table->unsignedInteger('document_status_id')->nullable()->comment('ID del stato del documento associato');
            }
        });
    }
};
