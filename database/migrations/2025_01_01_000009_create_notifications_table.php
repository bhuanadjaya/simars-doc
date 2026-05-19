<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->foreignUuid('document_id')
                  ->nullable()
                  ->constrained('documents')
                  ->nullOnDelete();
            $table->string('title', 150);
            $table->text('message');
            $table->enum('type', [
                'new_document',
                'document_obsolete',
                'new_regulation',
            ]);
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
