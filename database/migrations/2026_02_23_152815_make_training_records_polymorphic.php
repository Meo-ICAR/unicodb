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
        Schema::table('training_records', function (Blueprint $table) {
            // Add polymorphic columns
            $table->string('trainable_type')->nullable()->after('training_session_id')->comment('Classe del Modello collegato (es. App\Models\Employee, App\Models\Agent, etc.)');
            $table->string('trainable_id', 36)->nullable()->after('trainable_type')->comment('ID del Modello (VARCHAR 36 per supportare sia UUID che Integer)');

            // Add indexes for polymorphic columns
            $table->index(['trainable_type', 'trainable_id'], 'trainable_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('training_records', function (Blueprint $table) {
            $table->dropIndex('trainable_index');
            $table->dropColumn('trainable_type');
            $table->dropColumn('trainable_id');
        });
    }
};
