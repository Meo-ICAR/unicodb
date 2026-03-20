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
        Schema::table('companies', function (Blueprint $table) {
            $table->text('page_header')->nullable()->after('company_type_id')->comment('Intestazione per carta intestata');
            $table->text('page_footer')->nullable()->after('page_header')->comment('Piè di pagina per carta intestata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['page_header', 'page_footer']);
        });
    }
};
