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
            * Run The Admin Tags Permissions.
            */
            [
                'name' => 'tag_index',
                'display_name' => 'Tag Index',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'tag',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'tag_create',
                'display_name' => 'Tag Create',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'tag',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'tag_edit',
                'display_name' => 'Tag Edit',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'tag',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'tag_show',
                'display_name' => 'Tag Show',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'tag',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'tag_delete',
                'display_name' => 'Tag Delete',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'tag',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],

            /**
            * Run The Admin Departments Permissions.
            */
            [
                'name' => 'department_index',
                'display_name' => 'Department Index',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'department',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'department_create',
                'display_name' => 'Department Create',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'department',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'department_edit',
                'display_name' => 'Department Edit',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'department',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'department_show',
                'display_name' => 'Department Show',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'department',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'department_delete',
                'display_name' => 'Department Delete',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'department',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],

            /**
            * Run The Admin Category Permissions.
            */
            [
                'name' => 'category_index',
                'display_name' => 'Category Index',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'category',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'category_create',
                'display_name' => 'Category Create',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'category',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'category_edit',
                'display_name' => 'Category Edit',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'category',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'category_show',
                'display_name' => 'Category Show',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'category',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'category_delete',
                'display_name' => 'Category Delete',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'category',
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
            * Run The Admin Post Permissions.
            */
            [
                'name' => 'post_index',
                'display_name' => 'Post Index',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'post_create',
                'display_name' => 'Post Create',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'post_edit',
                'display_name' => 'Post Edit',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'post_show',
                'display_name' => 'Post Show',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'post_delete',
                'display_name' => 'Post Delete',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            
            /**
            * Run The Admin Share Post Permissions.
            */
            [
                'name' => 'shared_post_index',
                'display_name' => 'Share Post Index',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'share_post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'shared_post_create',
                'display_name' => 'Share Post Create',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'share_post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'shared_post_edit',
                'display_name' => 'Share Post Edit',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'share_post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'shared_post_show',
                'display_name' => 'Share Post Show',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'share_post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name' => 'shared_post_delete',
                'display_name' => 'Share Post Delete',
                'guard_name' => 'admin',
                'type' => 'general', // general, others
                'group' => 'share_post',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }
}