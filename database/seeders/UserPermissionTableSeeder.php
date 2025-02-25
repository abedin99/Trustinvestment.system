<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UserPermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::insert([
            [
                'name' => 'dashboard',
                'display_name' => 'Dashboard',
                'guard_name' => 'web',
                'type' => 'general', // general, others
                'group' => 'dashboard',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],

        ]);
    }
}