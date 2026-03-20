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
            $table
                ->unsignedInteger('practice_status_id')
                ->nullable()
                ->after('agent_id')
                ->comment('ID dello stato della pratica');

            $table
                ->foreign('practice_status_id')
                ->references('id')
                ->on('practice_statuses')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('practices', function (Blueprint $table) {
            $table->dropForeign('practices_practice_status_id_foreign');
            $table->dropColumn('practice_status_id');
        });
    }
};
