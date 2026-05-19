<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'id'          => Str::uuid(),
                'name'        => 'super_admin',
                'description' => 'Full access: upload, publish, obsolete, delete documents, manage users',
                'permissions' => json_encode([
                    'document.upload',
                    'document.publish',
                    'document.obsolete',
                    'document.delete',
                    'document.view',
                    'document.download',
                    'regulation.upload',
                    'regulation.view',
                    'user.manage',
                    'unit.manage',
                    'report.export',
                ]),
            ],
            [
                'id'          => Str::uuid(),
                'name'        => 'admin_unit',
                'description' => 'Upload & publish documents for own unit, view all active documents',
                'permissions' => json_encode([
                    'document.upload',
                    'document.publish',
                    'document.obsolete.own_unit',
                    'document.view',
                    'document.download',
                    'regulation.view',
                ]),
            ],
            [
                'id'          => Str::uuid(),
                'name'        => 'user',
                'description' => 'Read-only: view and download active documents',
                'permissions' => json_encode([
                    'document.view',
                    'document.download',
                    'regulation.view',
                ]),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}
