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
        Schema::table('audits', function (Blueprint $table) {
            $table->foreign(['regulatory_body_id'], 'audits_ibfk_4')->references(['id'])->on('regulatory_bodies')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['client_id'], 'audits_ibfk_5')->references(['id'])->on('clients')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audits', function (Blueprint $table) {
            $table->dropForeign('audits_ibfk_4');
            $table->dropForeign('audits_ibfk_5');
        });
    }
};
