<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::insert([
            [
                'name' => 'Demo User',
                'username' => 'demo',
                'email' => 'demo@user.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'remember_token' => Str::random(40),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        
        User::factory()->count(100)->create();
    }
}