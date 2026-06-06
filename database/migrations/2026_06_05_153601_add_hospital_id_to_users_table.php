<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // nullable: system_admin memiliki hospital_id = NULL
            $table->foreignUuid('hospital_id')
                ->nullable()
                ->after('id')
                ->constrained('hospitals')
                ->restrictOnDelete();

            $table->index('hospital_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['hospital_id']);
            $table->dropIndex(['hospital_id']);
            $table->dropColumn('hospital_id');
        });
    }
};
