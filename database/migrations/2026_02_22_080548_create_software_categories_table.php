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
        Schema::create('software_categories', function (Blueprint $table) {
            $table->comment('Tabella di lookup globale: Categorie di software utilizzati dalle agenzie.');
            $table->increments('id')->comment('ID univoco categoria');
            $table->string('name', 100)->unique()->comment('Es. CRM, Call Center, Contabilità, AML, Firma Elettronica');
            $table->string('code', 50)->unique()->comment('Codice tecnico (es. CRM, CALL_CENTER, ACC, AML)');
            $table->string('description')->nullable()->comment('Descrizione della tipologia di software');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_categories');
    }
};
