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
        Schema::table('api_configurations', function (Blueprint $table) {
            $table->foreign(['company_id'], 'api_configurations_ibfk_1')->references(['id'])->on('companies')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['software_application_id'], 'api_configurations_ibfk_2')->references(['id'])->on('software_applications')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('api_configurations', function (Blueprint $table) {
            $table->dropForeign('api_configurations_ibfk_1');
            $table->dropForeign('api_configurations_ibfk_2');
        });
    }
};
