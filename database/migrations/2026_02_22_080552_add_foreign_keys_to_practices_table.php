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
        Schema::table('practices', function (Blueprint $table) {
            $table->foreign(['company_id'], 'practices_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('cascade');

            $table->foreign(['practice_scope_id'], 'practices_ibfk_5')->references(['id'])->on('practice_scopes')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropForeign('practices_ibfk_1');
            $table->dropForeign('practices_ibfk_3');
            $table->dropForeign('practices_ibfk_4');
            $table->dropForeign('practices_ibfk_5');
        });
    }
};
