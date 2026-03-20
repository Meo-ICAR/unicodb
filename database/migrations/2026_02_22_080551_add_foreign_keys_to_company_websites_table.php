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
        Schema::table('company_websites', function (Blueprint $table) {
            $table->foreign(['company_id'], 'company_websites_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['principal_id'], 'company_websites_ibfk_2')->references(['id'])->on('principals')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_websites', function (Blueprint $table) {
            $table->dropForeign('company_websites_ibfk_1');
            $table->dropForeign('company_websites_ibfk_2');
        });
    }
};
