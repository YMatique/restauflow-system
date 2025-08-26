<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\System\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();
        $company1 = $companies->first();
        $company2 = $companies->last();

        // Obter categorias por empresa
        $pizzasCat1 = Category::where('company_id', $company1->id)->where('slug', 'pizzas')->first();
        $frutosCat1 = Category::where('company_id', $company1->id)->where('slug', 'frutos-do-mar')->first();
        $bebidasCat1 = Category::where('company_id', $company1->id)->where('slug', 'bebidas')->first();
        $sobremesasCat1 = Category::where('company_id', $company1->id)->where('slug', 'sobremesas')->first();
        $saladasCat1 = Category::where('company_id', $company1->id)->where('slug', 'saladas')->first();

        $pizzasCat2 = Category::where('company_id', $company2->id)->where('slug', 'pizzas')->first();
        $bebidasCat2 = Category::where('company_id', $company2->id)->where('slug', 'bebidas')->first();
        $sobremesasCat2 = Category::where('company_id', $company2->id)->where('slug', 'sobremesas')->first();
        $entradasCat2 = Category::where('company_id', $company2->id)->where('slug', 'entradas')->first();
        $cafesCat2 = Category::where('company_id', $company2->id)->where('slug', 'cafes')->first();

        // Produtos para Restaurante Sabores do Mar
        $productsCompany1 = [
            [
                'category_id' => $pizzasCat1->id,
                'name' => 'Pizza Margherita',
                'slug' => 'pizza-margherita',
                'description' => 'Pizza clássica com molho de tomate, mozzarella e manjericão fresco',
                'price' => 850.00,
                'cost' => 320.00,
                'type' => 'composed',
                'stock_quantity' => 25.0,
                'min_level' => 5.0,
                'cost_per_unit' => 320.00,
                'barcode' => '7891234567890',
                'track_stock' => true,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => true,
                'company_id' => $company1->id,
            ],
            [
                'category_id' => $pizzasCat1->id,
                'name' => 'Pizza Portuguesa',
                'slug' => 'pizza-portuguesa',
                'description' => 'Pizza com presunto, ovos, cebola, azeitonas e queijo',
                'price' => 1100.00,
                'cost' => 450.00,
                'type' => 'composed',
                'stock_quantity' => 18.0,
                'min_level' => 3.0,
                'cost_per_unit' => 450.00,
                'barcode' => '7891234567891',
                'track_stock' => true,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => false,
                'company_id' => $company1->id,
            ],
            [
                'category_id' => $frutosCat1->id,
                'name' => 'Camarão Grelhado',
                'slug' => 'camarao-grelhado',
                'description' => 'Camarões grelhados com alho e ervas finas',
                'price' => 1450.00,
                'cost' => 680.00,
                'type' => 'simple',
                'stock_quantity' => 12.0,
                'min_level' => 2.0,
                'cost_per_unit' => 680.00,
                'barcode' => '7891234567892',
                'track_stock' => true,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => true,
                'company_id' => $company1->id,
            ],
            [
                'category_id' => $frutosCat1->id,
                'name' => 'Peixe Grelhado do Dia',
                'slug' => 'peixe-grelhado-do-dia',
                'description' => 'Peixe fresco grelhado com legumes da época',
                'price' => 1200.00,
                'cost' => 520.00,
                'type' => 'simple',
                'stock_quantity' => 8.0,
                'min_level' => 2.0,
                'cost_per_unit' => 520.00,
                'barcode' => '7891234567893',
                'track_stock' => true,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => false,
                'company_id' => $company1->id,
            ],
            [
                'category_id' => $bebidasCat1->id,
                'name' => 'Coca-Cola 500ml',
                'slug' => 'coca-cola-500ml',
                'description' => 'Refrigerante Coca-Cola gelado',
                'price' => 120.00,
                'cost' => 45.00,
                'type' => 'simple',
                'stock_quantity' => 3.0,
                'min_level' => 10.0,
                'cost_per_unit' => 45.00,
                'barcode' => '7891000000123',
                'track_stock' => true,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => false,
                'company_id' => $company1->id,
            ]
        ];

        // Produtos para Pizzaria Milano
        $productsCompany2 = [
            [
                'category_id' => $pizzasCat2->id,
                'name' => 'Pizza Pepperoni',
                'slug' => 'pizza-pepperoni',
                'description' => 'Pizza com molho de tomate, mozzarella e pepperoni',
                'price' => 950.00,
                'cost' => 380.00,
                'type' => 'composed',
                'stock_quantity' => 20.0,
                'min_level' => 4.0,
                'cost_per_unit' => 380.00,
                'barcode' => '7892345678901',
                'track_stock' => true,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => true,
                'company_id' => $company2->id,
            ],
            [
                'category_id' => $pizzasCat2->id,
                'name' => 'Pizza Quattro Stagioni',
                'slug' => 'pizza-quattro-stagioni',
                'description' => 'Pizza dividida em quatro sabores diferentes',
                'price' => 1200.00,
                'cost' => 480.00,
                'type' => 'composed',
                'stock_quantity' => 15.0,
                'min_level' => 3.0,
                'cost_per_unit' => 480.00,
                'barcode' => '7892345678902',
                'track_stock' => true,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => false,
                'company_id' => $company2->id,
            ],
            [
                'category_id' => $sobremesasCat2->id,
                'name' => 'Tiramisu',
                'slug' => 'tiramisu',
                'description' => 'Sobremesa italiana clássica com café e mascarpone',
                'price' => 380.00,
                'cost' => 150.00,
                'type' => 'simple',
                'stock_quantity' => 6.0,
                'min_level' => 2.0,
                'cost_per_unit' => 150.00,
                'barcode' => '7892345678903',
                'track_stock' => true,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => true,
                'company_id' => $company2->id,
            ],
            [
                'category_id' => $entradasCat2->id,
                'name' => 'Pão de Alho',
                'slug' => 'pao-de-alho',
                'description' => 'Pão italiano com alho e ervas',
                'price' => 280.00,
                'cost' => 95.00,
                'type' => 'simple',
                'stock_quantity' => 12.0,
                'min_level' => 5.0,
                'cost_per_unit' => 95.00,
                'barcode' => '7892345678904',
                'track_stock' => true,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => false,
                'company_id' => $company2->id,
            ],
            [
                'category_id' => $cafesCat2->id,
                'name' => 'Espresso Italiano',
                'slug' => 'espresso-italiano',
                'description' => 'Café espresso autêntico italiano',
                'price' => 180.00,
                'cost' => 35.00,
                'type' => 'simple',
                'stock_quantity' => 2, // Produto sem controle de stock
                'min_level' => 0,
                'cost_per_unit' => 35.00,
                'barcode' => '7892345678905',
                'track_stock' => false,
                'is_available' => true,
                'is_active' => true,
                'is_featured' => false,
                'company_id' => $company2->id,
            ]
        ];

        $allProducts = array_merge($productsCompany1, $productsCompany2);

        foreach ($allProducts as $productData) {
            Product::create($productData);
        }
    }
}
