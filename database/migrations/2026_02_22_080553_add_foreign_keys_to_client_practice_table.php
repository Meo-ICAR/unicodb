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
        Schema::table('client_practice', function (Blueprint $table) {
            $table->foreign(['practice_id'], 'client_practice_ibfk_1')->references(['id'])->on('practices')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['client_id'], 'client_practice_ibfk_2')->references(['id'])->on('clients')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['company_id'], 'client_practice_ibfk_3')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_practice', function (Blueprint $table) {
            $table->dropForeign('client_practice_ibfk_1');
            $table->dropForeign('client_practice_ibfk_2');
            $table->dropForeign('client_practice_ibfk_3');
        });
    }
};
