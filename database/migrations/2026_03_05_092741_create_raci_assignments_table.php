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
        Schema::create('raci_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_task_id')->constrained('process_tasks')->cascadeOnDelete();
            $table->foreignId('business_function_id')->constrained('business_functions')->cascadeOnDelete();
            $table->enum('role', ['R', 'A', 'C', 'I']);
            $table->timestamps();

            $table->unique(['process_task_id', 'business_function_id'], 'unique_raci_task');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raci_assignments');
    }
};
