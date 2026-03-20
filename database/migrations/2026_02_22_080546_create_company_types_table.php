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
        Schema::create('company_types', function (Blueprint $table) {
            $table->comment('Tabella di lookup globale (Senza Tenant): Forme giuridiche delle società.');
            $table->increments('id')->comment('ID intero autoincrementante');
            $table->string('name')->comment('Es. S.p.A., S.r.l., Ditta Individuale');
            $table->timestamp('created_at')->nullable()->useCurrent()->comment('Data di creazione');
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent()->comment('Ultima modifica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_types');
    }
};
