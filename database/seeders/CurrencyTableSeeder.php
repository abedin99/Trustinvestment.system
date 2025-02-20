<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'name'       => 'United States dollar',
                'slug'       => Str::random('11'),
                'symbol'     => '$',
                'currency'   => 'USD',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'United Arab Emirates Dirham',
                'slug'       => Str::random('11'),
                'symbol'     => 'د.إ‎',
                'currency'   => 'AED',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Bangladesh Taka',
                'slug'       => Str::random('11'),
                'symbol'     => '৳',
                'currency'   => 'BDT',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Indian Rupee ',
                'slug'       => Str::random('11'),
                'symbol'     => '₹',
                'currency'   => 'INR',
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($currencies as $key => $currency) {
            Currency::insert($currency);
        }
    }
}