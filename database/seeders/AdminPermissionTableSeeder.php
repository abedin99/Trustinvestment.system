<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AdminPermissionTableSeeder extends Seeder
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
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'dashboard',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],

            /**
            * Run The Admin Role Permissions.
            */
            [
                'name' => 'role_index',
                'display_name' => 'Role Index',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'role',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'role_create',
                'display_name' => 'Role Create',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'role',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'role_edit',
                'display_name' => 'Role Edit',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'role',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'role_show',
                'display_name' => 'Role Show',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'role',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'role_delete',
                'display_name' => 'Role Delete',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'role',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'role_permissions',
                'display_name' => 'Role Permissions',
                'guard_name' => 'admin',
                'type' => 'others', // general, others
                'group' => 'role',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'role_force_delete',
                'display_name' => 'Role Force Delete',
                'guard_name' => 'admin',
                'type' => 'others', // general, others
                'group' => 'role',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],

            /**
            * Run The Admin User Permissions.
            */
            [
                'name' => 'admin_index',
                'display_name' => 'Admin Index',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'admin',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'admin_create',
                'display_name' => 'Admin Create',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'admin',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'admin_edit',
                'display_name' => 'Admin Edit',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'admin',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'admin_show',
                'display_name' => 'Admin Show',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'admin',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'admin_delete',
                'display_name' => 'Admin Delete',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'admin',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            
            /**
            * Run The Admin User Permissions.
            */
            [
                'name' => 'user_index',
                'display_name' => 'User Index',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'user',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'user_create',
                'display_name' => 'User Create',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'user',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'user_edit',
                'display_name' => 'User Edit',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'user',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'user_show',
                'display_name' => 'User Show',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'user',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'user_delete',
                'display_name' => 'User Delete',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'user',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],

            /**
            * Run The Currency Permissions.
            */
            [
                'name' => 'currency_index',
                'display_name' => 'Currency Index',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'currency',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'currency_create',
                'display_name' => 'Currency Create',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'currency',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'currency_edit',
                'display_name' => 'Currency Edit',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'currency',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'currency_show',
                'display_name' => 'Currency Show',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'currency',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'currency_delete',
                'display_name' => 'Currency Delete',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'currency',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'currency_force_delete',
                'display_name' => 'Currency Force Delete',
                'guard_name' => 'admin',
                'type' => 'others', // general, others
                'group' => 'currency',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],

        ]);
    }
}