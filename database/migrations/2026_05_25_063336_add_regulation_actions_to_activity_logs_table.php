<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE activity_logs MODIFY COLUMN action ENUM(
            'login',
            'logout',
            'view_document',
            'download_document',
            'create_document',
            'edit_document',
            'publish_document',
            'set_obsolete',
            'delete_document',
            'upload_regulation',
            'update_regulation',
            'delete_regulation'
        ) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE activity_logs MODIFY COLUMN action ENUM(
            'login',
            'logout',
            'view_document',
            'download_document',
            'create_document',
            'edit_document',
            'publish_document',
            'set_obsolete',
            'delete_document',
            'upload_regulation'
        ) NOT NULL");
    }
};
