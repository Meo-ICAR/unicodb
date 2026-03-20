<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_type_scope', function (Blueprint $table) {
            $table->comment('Tabella pivot per associare uno o più ambiti (tag) a ogni tipologia di documento.');
            $table->unsignedInteger('document_type_id')->comment('ID tipo documento');
            $table->unsignedInteger('document_scope_id')->comment('ID ambito normativo');
            $table->string('name')->nullable()->comment('Descrizione');

            $table->primary(['document_type_id', 'document_scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_type_scope');
    }
};
