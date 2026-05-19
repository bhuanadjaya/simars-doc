<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('external_regulations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('regulation_number', 100);
            $table->string('title', 255);
            $table->string('issuing_agency', 150);
            $table->enum('category', [
                'law',
                'government_regulation',
                'ministerial_regulation',
                'ministerial_decree',
                'national_standard',
                'accreditation_standard',
                'bpjs_regulation',
                'other',
            ]);
            $table->date('issued_date');
            $table->date('effective_date');
            $table->string('file_path', 500);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->json('affected_unit_ids')->nullable();
            $table->foreignUuid('uploaded_by')
                  ->constrained('users')
                  ->restrictOnDelete();
            $table->timestamps();

            $table->index('category');
            $table->index('status');
            $table->fullText(['regulation_number', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('external_regulations');
    }
};
