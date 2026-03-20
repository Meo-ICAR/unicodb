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
            $table->text('annotation')->nullable()->comment('Annotazioni documento');
            $table->string('url_document')->nullable()->comment('Url pubblicazione documento');
            $table->text('description')->nullable()->comment('Descrizione documento');
            $table->unsignedInteger('document_status_id')->nullable()->comment('ID stato documento');
            $table->boolean('is_signed')->default(false)->comment('Indica se il documento è firmato');
            $table->foreignId('user_id')->nullable()->comment('ID utente associato')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['annotation', 'description', 'document_status_id', 'is_signed', 'user_id']);
        });
    }
};
