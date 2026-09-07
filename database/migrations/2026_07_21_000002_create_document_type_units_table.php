<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_type_units', function (Blueprint $table) {
            $table->foreignUuid('document_type_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('unit_id')->constrained()->cascadeOnDelete();
            $table->primary(['document_type_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_type_units');
    }
};
