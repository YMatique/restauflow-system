<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\System\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        // Categorias para Restaurante Sabores do Mar
        $categoriesCompany1 = [
            [
                'name' => 'Pizzas',
                'slug' => 'pizzas',
                'color' => '#ef4444',
                'emoji' => '🍕',
                'description' => 'Pizzas tradicionais e especiais',
                'sort_order' => 1,
                'is_active' => true,
                'company_id' => $companies->first()->id,
            ],
            [
                'name' => 'Frutos do Mar',
                'slug' => 'frutos-do-mar',
                'color' => '#3b82f6',
                'emoji' => '🦐',
                'description' => 'Peixes, camarões e pratos do mar',
                'sort_order' => 2,
                'is_active' => true,
                'company_id' => $companies->first()->id,
            ],
            [
                'name' => 'Bebidas',
                'slug' => 'bebidas',
                'color' => '#10b981',
                'emoji' => '🥤',
                'description' => 'Refrigerantes, sucos e águas',
                'sort_order' => 3,
                'is_active' => true,
                'company_id' => $companies->first()->id,
            ],
            [
                'name' => 'Sobremesas',
                'slug' => 'sobremesas',
                'color' => '#f59e0b',
                'emoji' => '🍰',
                'description' => 'Doces e sobremesas da casa',
                'sort_order' => 4,
                'is_active' => true,
                'company_id' => $companies->first()->id,
            ],
            [
                'name' => 'Saladas',
                'slug' => 'saladas',
                'color' => '#22c55e',
                'emoji' => '🥗',
                'description' => 'Saladas frescas e nutritivas',
                'sort_order' => 5,
                'is_active' => true,
                'company_id' => $companies->first()->id,
            ]
        ];

        // Categorias para Pizzaria Milano
        $categoriesCompany2 = [
            [
                'name' => 'Pizzas',
                'slug' => 'pizzas',
                'color' => '#ef4444',
                'emoji' => '🍕',
                'description' => 'Pizzas italianas autênticas',
                'sort_order' => 1,
                'is_active' => true,
                'company_id' => $companies->last()->id,
            ],
            [
                'name' => 'Bebidas',
                'slug' => 'bebidas',
                'color' => '#10b981',
                'emoji' => '🥤',
                'description' => 'Refrigerantes e bebidas geladas',
                'sort_order' => 2,
                'is_active' => true,
                'company_id' => $companies->last()->id,
            ],
            [
                'name' => 'Sobremesas',
                'slug' => 'sobremesas',
                'color' => '#f59e0b',
                'emoji' => '🍰',
                'description' => 'Sobremesas italianas',
                'sort_order' => 3,
                'is_active' => true,
                'company_id' => $companies->last()->id,
            ],
            [
                'name' => 'Entradas',
                'slug' => 'entradas',
                'color' => '#8b5cf6',
                'emoji' => '🧄',
                'description' => 'Pães de alho e entradas',
                'sort_order' => 4,
                'is_active' => true,
                'company_id' => $companies->last()->id,
            ],
            [
                'name' => 'Cafés',
                'slug' => 'cafes',
                'color' => '#6b7280',
                'emoji' => '☕',
                'description' => 'Cafés especiais e expresso',
                'sort_order' => 5,
                'is_active' => true,
                'company_id' => $companies->last()->id,
            ]
        ];

        $allCategories = array_merge($categoriesCompany1, $categoriesCompany2);

        foreach ($allCategories as $categoryData) {
            Category::create($categoryData);
        }
    }
}
