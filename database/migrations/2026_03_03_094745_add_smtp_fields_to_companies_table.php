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
            $table->string('smtp_host')->nullable()->comment('Host server SMTP per invio email');
            $table->integer('smtp_port')->nullable()->comment('Porta server SMTP');
            $table->string('smtp_username')->nullable()->comment('Username SMTP');
            $table->string('smtp_password')->nullable()->comment('Password SMTP (encrypted)');
            $table->string('smtp_encryption')->nullable()->comment('Tipo crittografia SMTP (tls, ssl)');
            $table->string('smtp_from_email')->nullable()->comment('Email mittente per invio SMTP');
            $table->string('smtp_from_name')->nullable()->comment('Nome mittente per invio SMTP');
            $table->boolean('smtp_enabled')->default(false)->comment('Abilita invio email tramite SMTP');
            $table->boolean('smtp_verify_ssl')->default(true)->comment('Verifica certificato SSL SMTP');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'smtp_host',
                'smtp_port',
                'smtp_username',
                'smtp_password',
                'smtp_encryption',
                'smtp_from_email',
                'smtp_from_name',
                'smtp_enabled',
                'smtp_verify_ssl'
            ]);
        });
    }
};
