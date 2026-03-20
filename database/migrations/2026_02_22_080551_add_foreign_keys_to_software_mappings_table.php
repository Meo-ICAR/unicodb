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
        Schema::table('software_mappings', function (Blueprint $table) {
            $table->foreign(['software_application_id'], 'software_mappings_ibfk_1')->references(['id'])->on('software_applications')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('software_mappings', function (Blueprint $table) {
            $table->dropForeign('software_mappings_ibfk_1');
        });
    }
};
