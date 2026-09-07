<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HospitalSeeder extends Seeder
{
    // UUID yang sama dengan yang dipakai di migration untuk data existing
    const DEFAULT_HOSPITAL_ID = '019e0000-0000-7000-8000-000000000001';

    public function run(): void
    {
        DB::table('hospitals')->insertOrIgnore([
            'id'         => self::DEFAULT_HOSPITAL_ID,
            'name'       => 'RSUD Bhuana Djaya',
            'code'       => 'RSBD',
            'address'    => 'Jl. Contoh No. 1, Jakarta',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Hospital default seeded: RSUD Bhuana Djaya (ID: ' . self::DEFAULT_HOSPITAL_ID . ')');
    }
}
