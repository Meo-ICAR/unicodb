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
        Schema::create('proforma_status', function (Blueprint $table) {
            $table->comment('Stati del proforma');
            $table->integer('id', true);
            $table->string('name')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_deleted')->nullable();
            $table->boolean('is_payable')->nullable();
            $table->boolean('is_external')->nullable();
            $table->boolean('is_ok')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proforma_status');
    }
};
