<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_files', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('document_id')
                  ->constrained('documents')
                  ->cascadeOnDelete();
            $table->enum('file_type', ['pdf', 'docx']);
            $table->string('original_filename', 255);
            $table->string('file_path', 500);
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type', 100);
            $table->foreignUuid('uploaded_by')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->timestamp('uploaded_at')->useCurrent();

            $table->index('document_id');
            $table->unique(['document_id', 'file_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_files');
    }
};
