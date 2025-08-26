<?php

namespace Database\Seeders;

use App\Models\System\Company;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        $company1 = $companies->first();
        $company2 = $companies->last();

        $users = [
            [
                'name' => 'António Silva',
                'email' => 'antonio.silva@saboresdomar.mz',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'is_active' => true,
                'phone' => '+258 84 123 4567',
                'company_id' => $company1->id,
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@saboresdomar.mz',
                'password' => Hash::make('password123'),
                'role' => 'manager',
                'is_active' => true,
                'phone' => '+258 84 234 5678',
                'company_id' => $company1->id,
            ],
            [
                'name' => 'João Fernandes',
                'email' => 'joao.fernandes@saboresdomar.mz',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'is_active' => true,
                'phone' => '+258 84 345 6789',
                'company_id' => $company1->id,
            ],
            [
                'name' => 'Carlos Pereira',
                'email' => 'carlos@pizzariamilano.mz',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'is_active' => true,
                'phone' => '+258 84 456 7890',
                'company_id' => $company2->id,
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@pizzariamilano.mz',
                'password' => Hash::make('password123'),
                'role' => 'cashier',
                'is_active' => true,
                'phone' => '+258 84 567 8901',
                'company_id' => $company2->id,
            ]
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
