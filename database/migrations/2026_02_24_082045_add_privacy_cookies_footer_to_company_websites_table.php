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
            $table->string('url_privacy')->nullable()->after('is_active')->comment('URL pagina privacy policy');
            $table->string('url_cookies')->nullable()->after('url_privacy')->comment('URL pagina cookie policy');
            $table->boolean('is_footercompilant')->default(false)->after('url_cookies')->comment('True se il footer è conforme GDPR');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_websites', function (Blueprint $table) {
            $table->dropColumn(['url_privacy', 'url_cookies', 'is_footercompilant']);
        });
    }
};
