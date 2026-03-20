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
        Schema::create('practice_oam_base', function (Blueprint $table) {
            $table->id();
            $table->string('B_OAM')->nullable();  // oam_name

            // Conteggi
            $table->integer('C_Convenzionata')->default(0);
            $table->integer('D_Non_Convenzionata')->default(0);
            $table->integer('E_Intermediate')->default(0);
            $table->integer('F_Lavorazione')->default(0);

            // Valori Monetari
            $table->decimal('G_Erogato', 15, 2)->default(0);
            $table->decimal('H_Erogato_Lavorazione', 15, 2)->default(0);
            $table->decimal('I_Provvigione_Cliente', 15, 2)->default(0);
            $table->decimal('J_Provvigione_Istituto', 15, 2)->default(0);
            $table->decimal('K_Provvigione_Istituto_Lavorazione', 15, 2)->default(0);
            $table->decimal('O_Provvigione_Rete', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('practice_oam_base');
    }
};
