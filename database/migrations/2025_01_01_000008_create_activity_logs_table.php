<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignUuid('document_id')
                  ->nullable()
                  ->constrained('documents')
                  ->nullOnDelete();
            $table->enum('action', [
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
            ]);
            $table->text('detail')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('document_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
