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
        Schema::table('company_websites', function (Blueprint $table) {
            $table
                ->boolean('is_typical')
                ->default(true)
                ->after('is_active')
                ->comment('Sito utilizzato per attività tipica');
            $table
                ->date('privacy_date')
                ->nullable()
                ->after('is_typical')
                ->comment('Data aggiornamento privacy');
            $table
                ->date('transparency_date')
                ->nullable()
                ->after('privacy_date')
                ->comment('Data aggiornamento trasparenza');
            $table
                ->date('privacy_prior_date')
                ->nullable()
                ->after('transparency_date')
                ->comment('Precedente aggiornamento privacy');
            $table
                ->date('transparency_prior_date')
                ->nullable()
                ->after('privacy_prior_date')
                ->comment('Precedente aggiornamento trasparenza');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_websites', function (Blueprint $table) {
            $table->dropColumn([
                'is_typical',
                'privacy_date',
                'transparency_date',
                'privacy_prior_date',
                'transparency_prior_date'
            ]);
        });
    }
};
