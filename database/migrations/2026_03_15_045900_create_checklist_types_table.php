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
        Schema::create('checklist_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_compliance')->default(true);
            $table->string('color')->nullable();
            $table->unsignedInteger('regulatory_body_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_types');
    }
};
