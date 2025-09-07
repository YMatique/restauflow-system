<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Str;

class SubcategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories
        $categories = Category::all();

        foreach ($categories as $category) {
            // Example subcategories per category
            $subcategoriesData = [
                [
                    'name' => $category->name . ' Sub 1',
                    'emoji' => '⭐',
                    'sort_order' => 1,
                ],
                [
                    'name' => $category->name . ' Sub 2',
                    'emoji' => '🌟',
                    'sort_order' => 2,
                ],
                [
                    'name' => $category->name . ' Sub 3',
                    'emoji' => '🔥',
                    'sort_order' => 3,
                ],
            ];

            foreach ($subcategoriesData as $subcat) {
                Subcategory::create([
                    'category_id' => $category->id,
                    'company_id' => $category->company_id,
                    'name' => $subcat['name'],
                    'slug' => Str::slug($subcat['name']),
                    'color' => '#3B82F6', // default color
                    'emoji' => $subcat['emoji'] ?? null,
                    'description' => $subcat['name'] . ' description',
                    'sort_order' => $subcat['sort_order'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
