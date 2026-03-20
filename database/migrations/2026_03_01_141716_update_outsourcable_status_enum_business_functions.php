<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('business_functions', function (Blueprint $table) {
            $table->enum('outsourcable_status', ['yes', 'no', 'partial'])->default('no')->change();
        });
    }

    public function down()
    {
        Schema::table('business_functions', function (Blueprint $table) {
            $table->enum('outsourcable_status', ['si', 'no', 'parziale'])->default('no')->change();
        });
    }
};
