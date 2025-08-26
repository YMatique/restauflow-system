<?php

namespace Database\Seeders;

use App\Models\System\Company;
use App\Models\Table;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $companies = Company::all();

        // Mesas para Restaurante Sabores do Mar (8 mesas)
        $tablesCompany1 = [
            [
                'number' => 1,
                'name' => 'Mesa 1',
                'capacity' => 4,
                'status' => 'available',
                'location' => 'Salão Principal',
                'shape' => 'square',
                'position' => ['x' => 100, 'y' => 100],
                'notes' => 'Mesa próxima à janela',
                'is_active' => true,
                'service_charge' => 10.00,
                'company_id' => $companies->first()->id,
            ],
            [
                'number' => 2,
                'name' => 'Mesa 2',
                'capacity' => 2,
                'status' => 'available',
                'location' => 'Salão Principal',
                'shape' => 'round',
                'position' => ['x' => 200, 'y' => 100],
                'notes' => 'Mesa para casal',
                'is_active' => true,
                'service_charge' => 10.00,
                'company_id' => $companies->first()->id,
            ],
            [
                'number' => 3,
                'name' => 'Mesa 3',
                'capacity' => 6,
                'status' => 'occupied',
                'location' => 'Salão Principal',
                'shape' => 'rectangular',
                'position' => ['x' => 300, 'y' => 100],
                'notes' => 'Mesa para grupos',
                'is_active' => true,
                'service_charge' => 15.00,
                'company_id' => $companies->first()->id,
            ],
            [
                'number' => 4,
                'name' => 'Mesa 4',
                'capacity' => 4,
                'status' => 'available',
                'location' => 'Salão Principal',
                'shape' => 'square',
                'position' => ['x' => 100, 'y' => 200],
                'notes' => null,
                'is_active' => true,
                'service_charge' => 10.00,
                'company_id' => $companies->first()->id,
            ],
            [
                'number' => 5,
                'name' => 'Mesa 5',
                'capacity' => 8,
                'status' => 'reserved',
                'location' => 'Salão VIP',
                'shape' => 'rectangular',
                'position' => ['x' => 200, 'y' => 200],
                'notes' => 'Mesa VIP com vista para o mar',
                'is_active' => true,
                'service_charge' => 20.00,
                'company_id' => $companies->first()->id,
            ],
            [
                'number' => 6,
                'name' => 'Mesa 6',
                'capacity' => 2,
                'status' => 'available',
                'location' => 'Terraço',
                'shape' => 'round',
                'position' => ['x' => 300, 'y' => 200],
                'notes' => 'Mesa no terraço',
                'is_active' => true,
                'service_charge' => 10.00,
                'company_id' => $companies->first()->id,
            ]
        ];

        // Mesas para Pizzaria Milano (6 mesas)
        $tablesCompany2 = [
            [
                'number' => 1,
                'name' => 'Mesa 1',
                'capacity' => 4,
                'status' => 'available',
                'location' => 'Área Principal',
                'shape' => 'square',
                'position' => ['x' => 100, 'y' => 100],
                'notes' => null,
                'is_active' => true,
                'service_charge' => 0,
                'company_id' => $companies->last()->id,
            ],
            [
                'number' => 2,
                'name' => 'Mesa 2',
                'capacity' => 2,
                'status' => 'occupied',
                'location' => 'Área Principal',
                'shape' => 'round',
                'position' => ['x' => 200, 'y' => 100],
                'notes' => null,
                'is_active' => true,
                'service_charge' => 0,
                'company_id' => $companies->last()->id,
            ],
            [
                'number' => 3,
                'name' => 'Mesa 3',
                'capacity' => 6,
                'status' => 'available',
                'location' => 'Área Família',
                'shape' => 'rectangular',
                'position' => ['x' => 300, 'y' => 100],
                'notes' => 'Mesa para famílias',
                'is_active' => true,
                'service_charge' => 0,
                'company_id' => $companies->last()->id,
            ],
            [
                'number' => 4,
                'name' => 'Mesa 4',
                'capacity' => 4,
                'status' => 'available',
                'location' => 'Área Principal',
                'shape' => 'square',
                'position' => ['x' => 100, 'y' => 200],
                'notes' => null,
                'is_active' => true,
                'service_charge' => 0,
                'company_id' => $companies->last()->id,
            ],
            [
                'number' => 5,
                'name' => 'Mesa 5',
                'capacity' => 2,
                'status' => 'available',
                'location' => 'Área Principal',
                'shape' => 'round',
                'position' => ['x' => 200, 'y' => 200],
                'notes' => null,
                'is_active' => true,
                'service_charge' => 0,
                'company_id' => $companies->last()->id,
            ],
            [
                'number' => 6,
                'name' => 'Mesa 6',
                'capacity' => 4,
                'status' => 'maintenance',
                'location' => 'Área Principal',
                'shape' => 'square',
                'position' => ['x' => 300, 'y' => 200],
                'notes' => 'Mesa em manutenção - cadeira quebrada',
                'is_active' => false,
                'service_charge' => 0,
                'company_id' => $companies->last()->id,
            ]
        ];

        $allTables = array_merge($tablesCompany1, $tablesCompany2);

        foreach ($allTables as $tableData) {
            Table::create($tableData);
        }
    }
}
