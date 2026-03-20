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
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->comment('Sessioni reali di formazione erogate o pianificate dalle agenzie.');
            $table->increments('id')->comment('ID univoco sessione');
            // Questa DEVE essere char(36) per combaciare con companies.id
            $table->char('company_id', 36)->nullable();

            // Ora il vincolo funzionerà
            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('set null');  // o cascade
            $table->unsignedInteger('training_template_id')->index('training_template_id')->comment('Riferimento al template del corso')->nullable();
            $table->string('name')->comment('Nome specifico (es. Sessione Autunnale OAM Roma)')->nullable()->default('');
            $table->decimal('total_hours', 5)->comment('Numero ore effettive erogate in questa sessione')->nullable()->default(1);
            $table->string('trainer_name')->nullable()->comment('Nome del docente o ente formatore');
            $table->date('start_date')->comment('Data inizio corso')->nullable();
            $table->date('end_date')->comment('Data fine corso')->nullable();
            $table->enum('location', ['ONLINE', 'PRESENZA', 'IBRIDO'])->default('ONLINE')->comment('Modalità di erogazione');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
