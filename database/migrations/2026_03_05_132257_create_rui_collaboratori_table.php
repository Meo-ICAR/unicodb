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
        Schema::create('rui_collaboratori', function (Blueprint $table) {
            $table->comment('Tabella collaboratori RUI (Registro Unico degli Intermediari)');
            $table->bigIncrements('id')->comment('ID autoincrementante');
            $table->string('oss')->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Codice OSS');
            $table->string('livello', 10)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Livello collaboratore');
            $table->string('num_iscr_intermediario', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Numero iscrizione intermediario');
            $table->string('num_iscr_collaboratori_i_liv', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Numero iscrizione collaboratori I livello');
            $table->string('num_iscr_collaboratori_ii_liv', 50)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Numero iscrizione collaboratori II livello');
            $table->string('qualifica_rapporto', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable()->comment('Qualifica rapporto');
            $table->string('intermediario', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('collaboratore', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->string('dipendente', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci')->nullable();
            $table->timestamps();

            // Set table charset and collation
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            // Add specific indexes with names as specified
            $table->index('num_iscr_intermediario', 'rui_collaboratori_num_iscr_intermediario_index');
            $table->index('num_iscr_collaboratori_i_liv', 'rui_collaboratori_num_iscr_collaboratori_i_liv_index');
            $table->index('num_iscr_collaboratori_ii_liv', 'rui_collaboratori_num_iscr_collaboratori_ii_liv_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rui_collaboratori');
    }
};
