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
        Schema::create('regulatory_body_scopes', function (Blueprint $table) {
            $table->comment('Tabella pivot per definire quali ambiti normativi sono di competenza di ciascun ente.');
            $table->unsignedInteger('regulatory_body_id')->comment("Riferimento all'ente");
            $table->unsignedInteger('document_scope_id')->comment("Riferimento all'ambito (es. Privacy, AML, OAM)");
            $table->string('name')->nullable()->comment('Descrizione');

            $table->primary(['regulatory_body_id', 'document_scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regulatory_body_scopes');
    }
};
