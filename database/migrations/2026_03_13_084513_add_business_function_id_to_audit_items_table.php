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
        Schema::table('audit_items', function (Blueprint $table) {
            $table
                ->foreignId('business_function_id')
                ->nullable()
                ->comment('ID della funzione business associata')
                ->constrained('business_functions')
                ->nullOnDelete();

            // Add index for performance
            $table->index('business_function_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_items', function (Blueprint $table) {
            $table->dropForeign(['business_function_id']);
            $table->dropIndex(['business_function_id']);
            $table->dropColumn('business_function_id');
        });
    }
};
