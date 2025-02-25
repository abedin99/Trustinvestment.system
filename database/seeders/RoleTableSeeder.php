<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::insert([
            [
                'name'        => 'Super-Admin',
                'slug'        => Str::slug('Super-Admin'),
                'guard_name'  => 'admin',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Admin',
                'slug'        => Str::slug('Admin'),
                'guard_name'  => 'admin',
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}