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
        Schema::create('compliance_violations', function (Blueprint $table) {
            $table->id();
            $table->char('company_id', 36);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->morphs('violatable');
            $table->string('violation_type');
            $table->enum('severity', ['basso', 'medio', 'alto', 'critico']);
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'violation_type']);
            $table->index(['company_id', 'severity']);
            $table->index(['company_id', 'resolved_at']);

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compliance_violations');
    }
};
