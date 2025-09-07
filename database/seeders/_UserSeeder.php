<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\System\Company;
use App\Models\Country;
use App\Models\Province;
use App\Models\City;
use App\Models\Role;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Cria roles padrão
        Role::seedDefaultRoles();
        $this->command->info('Roles padrão criadas com sucesso!');

        // Cria ou pega empresa padrão
        $company = Company::first() ?? Company::create([
            'public_id' => Str::uuid(),
            'name' => 'Empresa Padrão',
            'social_reason' => 'Empresa Padrão Lda',
            'nuit' => '123456789',
            'avatar' => 'company.png',
            'desc' => 'Esta é uma empresa de exemplo criada pelo seeder.',
            'status' => 'active',
        ]);


        // Usuários padrão
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

            // Associar role correspondente usando método seguro
            $role = Role::where('name', $userData['user_type'])->first();
            if ($role) {
                $user->roles()->syncWithoutDetaching($role->id);
                // syncWithoutDetaching evita duplicação
            }

        }

        $this->command->info('Usuários padrão criados e roles associadas com sucesso!');
    }
}
