<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = DB::table('roles')->pluck('id', 'name');
        $units = DB::table('units')->pluck('id', 'code');

        if ($roles->isEmpty() || $units->isEmpty()) {
            $this->command->warn('Seed dibatalkan: jalankan RoleSeeder dan UnitSeeder terlebih dahulu.');
            return;
        }

        $now   = now();
        $users = [
            // Super Admin
            [
                'id'          => Str::uuid()->toString(),
                'employee_id' => 'SA001',
                'name'        => 'Super Admin',
                'email'       => 'superadmin@simars-doc.com',
                'password'    => Hash::make('bhuanadjaya'),
                'unit_id'     => $units->first(),
                'role_id'     => $roles['super_admin'],
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],

            // Admin Unit — satu per unit
            [
                'id'          => Str::uuid()->toString(),
                'employee_id' => 'AU001',
                'name'        => 'Admin IGD',
                'email'       => 'admin.igd@simars-doc.com',
                'password'    => Hash::make('bhuanadjaya'),
                'unit_id'     => $units['IGD'],
                'role_id'     => $roles['admin_unit'],
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => Str::uuid()->toString(),
                'employee_id' => 'AU002',
                'name'        => 'Admin Rawat Inap',
                'email'       => 'admin.ranap@simars-doc.com',
                'password'    => Hash::make('bhuanadjaya'),
                'unit_id'     => $units['RANAP'],
                'role_id'     => $roles['admin_unit'],
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => Str::uuid()->toString(),
                'employee_id' => 'AU003',
                'name'        => 'Admin Rawat Jalan',
                'email'       => 'admin.rajal@simars-doc.com',
                'password'    => Hash::make('bhuanadjaya'),
                'unit_id'     => $units['RAJAL'],
                'role_id'     => $roles['admin_unit'],
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => Str::uuid()->toString(),
                'employee_id' => 'AU004',
                'name'        => 'Admin Laboratorium',
                'email'       => 'admin.lab@simars-doc.com',
                'password'    => Hash::make('bhuanadjaya'),
                'unit_id'     => $units['LAB'],
                'role_id'     => $roles['admin_unit'],
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'id'          => Str::uuid()->toString(),
                'employee_id' => 'AU005',
                'name'        => 'Admin Farmasi',
                'email'       => 'admin.farmasi@simars-doc.com',
                'password'    => Hash::make('bhuanadjaya'),
                'unit_id'     => $units['FARMASI'],
                'role_id'     => $roles['admin_unit'],
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],

            // User biasa
            [
                'id'          => Str::uuid()->toString(),
                'employee_id' => 'US001',
                'name'        => 'Pengguna Umum',
                'email'       => 'user@simars-doc.com',
                'password'    => Hash::make('bhuanadjaya'),
                'unit_id'     => $units->first(),
                'role_id'     => $roles['user'],
                'is_active'   => true,
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ];

        DB::table('users')->insert($users);

        $this->command->info('7 user berhasil dibuat.');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['super_admin', 'superadmin@simars-doc.com',   'bhuanadjaya'],
                ['admin_unit',  'admin.igd@simars-doc.com',    'bhuanadjaya'],
                ['admin_unit',  'admin.ranap@simars-doc.com',  'bhuanadjaya'],
                ['admin_unit',  'admin.rajal@simars-doc.com',  'bhuanadjaya'],
                ['admin_unit',  'admin.lab@simars-doc.com',    'bhuanadjaya'],
                ['admin_unit',  'admin.farmasi@simars-doc.com', 'bhuanadjaya'],
                ['user',        'user@simars-doc.com',         'bhuanadjaya'],
            ]
        );
    }
}
