<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            [
                'id'        => Str::uuid(),
                'code'      => 'IGD',
                'name'      => 'Instalasi Gawat Darurat',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'id'        => Str::uuid(),
                'code'      => 'RANAP',
                'name'      => 'Instalasi Rawat Inap',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'id'        => Str::uuid(),
                'code'      => 'RAJAL',
                'name'      => 'Instalasi Rawat Jalan',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'id'        => Str::uuid(),
                'code'      => 'LAB',
                'name'      => 'Instalasi Laboratorium',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'id'        => Str::uuid(),
                'code'      => 'FARMASI',
                'name'      => 'Instalasi Farmasi',
                'parent_id' => null,
                'is_active' => true,
            ],
        ];

        DB::table('units')->insert($units);
    }
}
