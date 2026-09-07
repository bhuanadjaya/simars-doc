<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_numbers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('document_id')->constrained()->cascadeOnDelete();
            $table->string('number', 100);
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();
            $table->index('document_id');
            $table->index('number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_numbers');
    }
};
