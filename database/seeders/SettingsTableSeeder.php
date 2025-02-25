<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $settings = [
            [
                'slug' => Str::random(),
                'option' => 'app_name',
                'value' => fake()->company(),
                'remarks' => null,
                'validation' => 'required|string|max:255',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => Str::random(),
                'option' => 'app_logo',
                'value' => asset('assets/img/logo.png'),
                'remarks' => null,
                'validation' => 'required|string|max:255',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => Str::random(),
                'option' => 'app_address',
                'value' => fake()->address(),
                'remarks' => null,
                'validation' => 'required|string|max:255',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => Str::random(),
                'option' => 'app_email',
                'value' => fake()->email(),
                'remarks' => null,
                'validation' => 'required|email|max:255',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => Str::random(),
                'option' => 'app_phone',
                'value' => fake()->phoneNumber(),
                'remarks' => null,
                'validation' => 'required|numeric|phone_number|size:11',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => Str::random(),
                'option' => 'currency',
                'value' => 1,
                'remarks' => null,
                'validation' => 'required|numeric',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $key => $setting) {
            Setting::insert($setting);
        }
    }
}
