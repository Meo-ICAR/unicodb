<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('coges', function (Blueprint $table) {
            $table->string('value_type')->default('Quadratura')->after('annotazioni')->comment('Tipo valore: Quadratura');
            $table->enum('value_period', ['Adesso', 'Oggi', 'Ieri', 'Settimana', 'Quindicinale', 'Mese', 'Trimestre'])
                ->default('Oggi')
                ->after('value_type')
                ->comment('Periodo di riferimento');
        });
    }

    public function down(): void
    {
        Schema::table('coges', function (Blueprint $table) {
            $table->dropColumn(['value_type', 'value_period']);
        });
    }
};
