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
        Schema::create('practice_commission_statuses', function (Blueprint $table) {
            $table->comment('Stati provvigioni pratiche');
            $table->tinyIncrements('id');
            $table->string('name', 255)->nullable()->comment('Stato pagamento');
            $table->string('code', 20)->nullable();
            $table->boolean('is_perfectioned')->nullable();
            $table->boolean('is_working')->nullable();
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_commission_statuses');
    }
};
