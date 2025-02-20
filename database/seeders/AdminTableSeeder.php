<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::insert([
            [
                'name' => 'Admin',
                'username' => 'admin',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Demo admin',
                'username' => 'demo',
                'email' => 'demo@admin.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
