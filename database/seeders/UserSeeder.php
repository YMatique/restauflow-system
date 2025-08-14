<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\System\Company;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $company = Company::first() ?? Company::create([
            'public_id' => Str::uuid(),
            'name' => 'Empresa Padrão',
            'social_reason' => 'Empresa Padrão Lda',
            'nuit' => '123456789',
            'avatar' => 'company.png',
            'desc' => 'Esta é uma empresa de exemplo criada pelo seeder.',
            'status' => 'active',
        ]);

        $country = Country::firstWhere('code', 'MZ'); // Moçambique
        if (!$country) {
            $this->command->info('País Moçambique não encontrado. Rode primeiro o CountrySeeder.');
            return;
        }

        $provinces = Province::where('country_id', $country->id)->get();
        $cities = City::all(); // cidades independentes

        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@example.com', 'user_type' => 'super_admin'],
            ['name' => 'Admin Empresa', 'email' => 'admin@example.com', 'user_type' => 'company_admin'],
            ['name' => 'Usuário Empresa', 'email' => 'user@example.com', 'user_type' => 'company_user'],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'public_id' => Str::uuid(),
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($userData['email']),
                    'company_id' => $company->id,
                    'user_type' => $userData['user_type'],
                    'status' => 'active',
                    'remember_token' => Str::random(10),
                ]
            );

            // Escolhe uma província e uma cidade aleatória
            $province = $provinces->random();
            $city = $cities->random();

            $user->addresses()->create([
                'public_id' => Str::uuid(),
                'country_id' => $country->id,
                'province_id' => $province->id,
                'city_id' => $city->id,
                'street' => $faker->streetAddress,
                'postalcode' => $faker->postcode,
            ]);

            // Telefones
            $user->telephones()->createMany([
                [
                    'public_id' => Str::uuid(),
                    'number' => $faker->numerify('8#######'),
                    'format' => '+258',
                    'type' => 'mobile',
                    'is_primary' => true,
                ],
                [
                    'public_id' => Str::uuid(),
                    'number' => $faker->numerify('8#######'),
                    'format' => '+258',
                    'type' => 'whatsapp',
                    'is_primary' => false,
                ],
            ]);

            // Emails adicionais
            $user->emails()->createMany([
                [
                    'public_id' => Str::uuid(),
                    'email' => $faker->unique()->safeEmail,
                    'is_primary' => true,
                ],
                [
                    'public_id' => Str::uuid(),
                    'email' => $faker->unique()->safeEmail,
                    'is_primary' => false,
                ],
            ]);
        }
    }
}
