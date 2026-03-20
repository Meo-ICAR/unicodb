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
        Schema::table('software_applications', function (Blueprint $table) {
            $table->foreign(['category_id'], 'software_applications_ibfk_1')->references(['id'])->on('software_categories')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('software_applications', function (Blueprint $table) {
            $table->dropForeign('software_applications_ibfk_1');
        });
    }
};
