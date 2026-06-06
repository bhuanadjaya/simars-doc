<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    // UUID tetap agar bisa direferensi di seeder dan migration lain
    const DEFAULT_HOSPITAL_ID = '019e0000-0000-7000-8000-000000000001';

    public function up(): void
    {
        // 1. Insert hospital default untuk data existing
        DB::table('hospitals')->insertOrIgnore([
            'id'         => self::DEFAULT_HOSPITAL_ID,
            'name'       => 'Rumah Sakit Default',
            'code'       => 'RS-DEFAULT',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $tables = ['units', 'document_types', 'documents', 'external_regulations', 'activity_logs', 'notifications'];

        // 2. Tambah kolom hospital_id (nullable dulu untuk backfill)
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->foreignUuid('hospital_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('hospitals')
                    ->restrictOnDelete();
                $t->index('hospital_id');
            });
        }

        // 3. Backfill semua data existing ke hospital default
        foreach ($tables as $table) {
            DB::table($table)->whereNull('hospital_id')
                ->update(['hospital_id' => self::DEFAULT_HOSPITAL_ID]);
        }

        // 4. Ubah jadi NOT NULL via raw SQL dengan FK checks disabled (MySQL strict FK restriction)
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table}` MODIFY COLUMN `hospital_id` CHAR(36) NOT NULL");
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        $tables = ['units', 'document_types', 'documents', 'external_regulations', 'activity_logs', 'notifications'];

        foreach (array_reverse($tables) as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['hospital_id']);
                $t->dropIndex(['hospital_id']);
                $t->dropColumn('hospital_id');
            });
        }
    }
};
