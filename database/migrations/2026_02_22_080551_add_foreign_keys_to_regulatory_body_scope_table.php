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
        Schema::table('regulatory_body_scopes', function (Blueprint $table) {
            $table->foreign(['regulatory_body_id'], 'regulatory_body_scope_ibfk_1')->references(['id'])->on('regulatory_bodies')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['document_scope_id'], 'regulatory_body_scope_ibfk_2')->references(['id'])->on('document_scopes')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regulatory_body_scope', function (Blueprint $table) {
            $table->dropForeign('regulatory_body_scope_ibfk_1');
            $table->dropForeign('regulatory_body_scope_ibfk_2');
        });
    }
};
