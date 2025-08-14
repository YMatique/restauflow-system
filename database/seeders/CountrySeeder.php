<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            ['name' => 'Mozambique', 'code' => 'MZ', 'currency_code' => 'MZN', 'currency_name' => 'Metical'],
            ['name' => 'South Africa', 'code' => 'ZA', 'currency_code' => 'ZAR', 'currency_name' => 'Rand'],
            ['name' => 'United States', 'code' => 'US', 'currency_code' => 'USD', 'currency_name' => 'Dollar'],
            ['name' => 'United Kingdom', 'code' => 'GB', 'currency_code' => 'GBP', 'currency_name' => 'Pound'],
            ['name' => 'Germany', 'code' => 'DE', 'currency_code' => 'EUR', 'currency_name' => 'Euro'],
            ['name' => 'Brazil', 'code' => 'BR', 'currency_code' => 'BRL', 'currency_name' => 'Real'],
            ['name' => 'France', 'code' => 'FR', 'currency_code' => 'EUR', 'currency_name' => 'Euro'],
            ['name' => 'Japan', 'code' => 'JP', 'currency_code' => 'JPY', 'currency_name' => 'Yen'],
        ];

        // Adiciona public_id (UUID) para cada país
        $countries = array_map(function ($country) {
            $country['public_id'] = Str::uuid();
            return $country;
        }, $countries);

        DB::table('countries')->insert($countries);
    }
}
