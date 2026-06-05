<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->date('expired_at')->nullable()->after('effective_date');
            $table->tinyInteger('reminder_months')->unsigned()->nullable()->after('expired_at');
            $table->enum('visibility', ['public', 'restricted'])->default('public')->after('reminder_months');
            $table->boolean('is_reviewed')->default(false)->after('visibility');
            $table->timestamp('reviewed_at')->nullable()->after('is_reviewed');
            $table->foreignUuid('reviewed_by')->nullable()->constrained('users')->nullOnDelete()->after('reviewed_at');
            $table->text('review_notes')->nullable()->after('reviewed_by');
        });

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
            'delete_regulation',
            'review_document',
            'unreview_document'
        ) NOT NULL");
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['expired_at', 'reminder_months', 'visibility', 'is_reviewed', 'reviewed_at', 'reviewed_by', 'review_notes']);
        });

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
};
