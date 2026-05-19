<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('number', 100);
            $table->string('title', 255);
            $table->foreignUuid('document_type_id')
                  ->constrained('document_types')
                  ->restrictOnDelete();
            $table->foreignUuid('owner_unit_id')
                  ->constrained('units')
                  ->restrictOnDelete();
            $table->foreignUuid('uploaded_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            $table->enum('source', ['internal', 'external'])->default('internal');
            $table->unsignedSmallInteger('revision_number')->default(0);
            $table->text('description')->nullable();
            $table->string('tags')->nullable();

            $table->enum('status', ['draft', 'active', 'obsolete'])->default('draft');
            $table->date('effective_date')->nullable();
            $table->date('published_at')->nullable();

            $table->date('obsolete_date')->nullable();
            $table->text('obsolete_reason')->nullable();
            $table->foreignUuid('obsoleted_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignUuid('replaced_by_id')
                  ->nullable()
                  ->constrained('documents')
                  ->nullOnDelete();

            $table->foreignUuid('parent_document_id')
                  ->nullable()
                  ->constrained('documents')
                  ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('document_type_id');
            $table->index('owner_unit_id');
            $table->index('number');
            $table->index('effective_date');
            $table->fullText(['title', 'description', 'tags']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
