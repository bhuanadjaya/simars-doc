<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'id'        => Str::uuid(),
                'code'      => 'SK',
                'name'      => 'Surat Keputusan',
                'is_active' => true,
            ],
            [
                'id'        => Str::uuid(),
                'code'      => 'PERDIRUT',
                'name'      => 'Peraturan Direktur',
                'is_active' => true,
            ],
            [
                'id'        => Str::uuid(),
                'code'      => 'SPO',
                'name'      => 'Standar Prosedur Operasional',
                'is_active' => true,
            ],
        ];

        DB::table('document_types')->insert($types);
    }
}
