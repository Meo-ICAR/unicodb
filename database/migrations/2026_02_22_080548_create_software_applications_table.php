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
        Schema::create('software_applications', function (Blueprint $table) {
            $table->comment('Tabella di lookup globale: Elenco dei software più comuni nel settore finanziario.');
            $table->increments('id')->comment('ID univoco software');
            $table->unsignedInteger('category_id')->comment('Riferimento alla categoria');
            $table->string('name')->comment('Nome commerciale (es. Salesforce, XCrm, Teamsystem, Namirial)');
            $table->string('provider_name')->nullable()->comment('Nome della software house produttrice');
            $table->string('website_url')->nullable()->comment('Sito web ufficiale del produttore');
            $table->string('api_url')->nullable();
            $table->string('sandbox_url')->nullable();
            $table->string('api_key_url')->nullable();
            $table->text('api_parameters')->nullable();
            $table->boolean('is_cloud')->nullable()->default(true)->comment('Indica se il software è SaaS/Cloud o On-Premise');
            $table->string('apikey')->nullable()->comment('API Key per il software');
            $table->decimal('wallet_balance', 10, 2)->nullable()->comment('Saldo del wallet');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_applications');
    }
};
