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
        Schema::table('agents', function (Blueprint $table) {
            $table->unsignedInteger('coordinated_by_id')->nullable()->after('company_id')->comment('ID del dipendente coordinatore');
            $table->unsignedInteger('coordinated_by_agent_id')->nullable()->after('coordinated_by_id')->comment("ID dell'agente coordinatore");
            $table->foreign(['coordinated_by_id'], 'agents_ibfk_2')->references(['id'])->on('employees')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['coordinated_by_agent_id'], 'agents_ibfk_3')->references(['id'])->on('agents')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropForeign('agents_ibfk_2');
            $table->dropForeign('agents_ibfk_3');
            $table->dropColumn(['coordinated_by_id', 'coordinated_by_agent_id']);
        });
    }
};
