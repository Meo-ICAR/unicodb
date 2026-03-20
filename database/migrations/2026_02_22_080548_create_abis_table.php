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
        Schema::create('abis', function (Blueprint $table) {
            $table->comment('Codici ABI bancari e intermediari finanziari italiani.');
            $table->bigIncrements('id')->comment('ID univoco interno');
            $table->string('abi', 5)->unique()->comment('Codice ABI a 5 cifre')->nullable();
            $table->string('name')->comment('Nome ufficiale (es. AGOS DUCATO S.P.A.)')->nullable();
            $table->enum('type', ['BANCA', 'INTERMEDIARIO_106', 'IP_IMEL'])->comment('Banca o Finanziaria ex Art. 106 TUB')->nullable();
            $table->string('capogruppo')->nullable()->comment('Gruppo bancario di appartenenza');
            $table->string('status')->default('OPERATIVO')->comment('OPERATIVO, CANCELLATO, IN_LIQUIDAZIONE')->nullable();
            $table->date('data_iscrizione')->nullable()->comment('Data di iscrizione al registro ABI');
            $table->date('data_cancellazione')->nullable()->comment('Data di cancellazione dal registro ABI');
            $table->timestamp('created_at')->nullable()->comment('Data creazione record');
            $table->timestamp('updated_at')->nullable()->comment('Data ultimo aggiornamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abis');
    }
};
