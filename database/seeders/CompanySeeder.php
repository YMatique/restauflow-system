<?php

namespace Database\Seeders;

use App\Models\System\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Restaurante Sabores do Mar',
                'slug' => 'sabores-do-mar',
                // 'email' => 'contato@saboresdomar.mz',
                // 'phone' => '+258 21 123 456',
                // 'address' => 'Avenida Julius Nyerere, 1234, Maputo, Moçambique',

                'nuit' => '400123456',
                // 'currency' => 'MZN',
                // 'tax_rate' => 17.00,
                'status' => 'active',
                'social_reason'=>'AINDA POR TRABALHAR',
                'logo' => null,
                'settings' => [
                    'timezone' => 'Africa/Maputo',
                    'language' => 'pt',
                    'date_format' => 'd/m/Y',
                    'time_format' => 'H:i',
                    'enable_reservations' => true,
                    'enable_delivery' => true,
                    'default_table_service_charge' => 10.00,
                    'pos_receipt_footer' => 'Obrigado pela sua preferência!',
                    'max_discount_percentage' => 20,
                    'allow_negative_stock' => false,
                ]
            ],
            [
                'name' => 'Pizzaria Milano',
                'slug' => 'pizzaria-milano',
                // 'email' => 'info@pizzariamilano.mz',
                // 'phone' => '+258 21 654 321',
                // 'address' => 'Rua da Marginal, 567, Beira, Moçambique',
                                'social_reason'=>'AINDA POR TRABALHAR',
                'nuit' => '400654321',
                // 'currency' => 'MZN',
                // 'tax_rate' => 17.00,
                'status' => 'active',
                'logo' => null,
                'settings' => [
                    'timezone' => 'Africa/Maputo',
                    'language' => 'pt',
                    'date_format' => 'd/m/Y',
                    'time_format' => 'H:i',
                    'enable_reservations' => false,
                    'enable_delivery' => true,
                    'default_table_service_charge' => 0,
                    'pos_receipt_footer' => 'A melhor pizza da cidade!',
                    'max_discount_percentage' => 15,
                    'allow_negative_stock' => false,
                ]
            ]
        ];

        foreach ($companies as $companyData) {
            Company::create($companyData);
        }
    }
}
