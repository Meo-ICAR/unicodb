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
        Schema::table('document_types', function (Blueprint $table) {
            $table->boolean('is_AiAbstract')->default(false)->comment('Ask AI to make abstract');
            $table->boolean('is_AiCheck')->default(false)->comment('AI conformity required');
            $table->text('AiPattern')->nullable()->comment('How AI can detect document is of this type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropColumn(['is_AiAbstract', 'is_AiCheck', 'AiPattern']);
        });
    }
};
