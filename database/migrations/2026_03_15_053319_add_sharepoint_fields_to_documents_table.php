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
        Schema::table('documents', function (Blueprint $table) {
            $table
                ->string('sharepoint_drive_id')
                ->nullable()
                ->after('sharepoint_id')
                ->comment('ID della Document Library di SharePoint');
            $table
                ->string('sharepoint_etag')
                ->nullable()
                ->after('sharepoint_drive_id')
                ->comment('Tag per il controllo della cache e delle versioni di SharePoint');
            $table
                ->string('sync_status')
                ->default('local')
                ->after('sharepoint_etag')
                ->comment('Stato della sincronizzazione file: local, syncing, synced, failed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'sharepoint_drive_id',
                'sharepoint_etag',
                'sync_status'
            ]);
        });
    }
};
