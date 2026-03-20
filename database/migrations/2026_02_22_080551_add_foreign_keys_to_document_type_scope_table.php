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
        Schema::table('document_type_scope', function (Blueprint $table) {
            $table->foreign(['document_type_id'], 'document_type_scope_ibfk_1')->references(['id'])->on('document_types')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['document_scope_id'], 'document_type_scope_ibfk_2')->references(['id'])->on('document_scopes')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_type_scope', function (Blueprint $table) {
            $table->dropForeign('document_type_scope_ibfk_1');
            $table->dropForeign('document_type_scope_ibfk_2');
        });
    }
};
