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
        // La tabella practice_statuses non ha practice_id
        // È una tabella di lookup per gli stati, non ha bisogno di foreign key
        // La foreign key verrà aggiunta in practice_status_history se necessario
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // La tabella practice_statuses non ha foreign keys da rimuovere
        // È una tabella di lookup per gli stati
    }
};
