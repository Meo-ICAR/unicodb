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
        Schema::table('principal_scopes', function (Blueprint $table) {
            $table->foreign(['practice_scope_id'], 'principal_scopes_ibfk_2')->references(['id'])->on('practice_scopes')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['principal_id'], 'principal_scopes_ibfk_3')->references(['id'])->on('principals')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('principal_scopes', function (Blueprint $table) {
            $table->dropForeign('principal_scopes_ibfk_2');
            $table->dropForeign('principal_scopes_ibfk_3');
        });
    }
};
