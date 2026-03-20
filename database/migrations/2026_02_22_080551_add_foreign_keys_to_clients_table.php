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
        Schema::table('clients', function (Blueprint $table) {
            $table->foreign(['company_id'], 'clients_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['client_type_id'], 'clients_ibfk_2')->references(['id'])->on('client_types')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign('clients_ibfk_1');
            $table->dropForeign('clients_ibfk_2');
        });
    }
};
