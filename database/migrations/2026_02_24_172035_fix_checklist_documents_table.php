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
        Schema::dropIfExists('checklist_documents');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Non faccio nulla, la tabella verrà ricreata dalla migration successiva
    }
};
