<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\Product;

class ProductForm extends Form
{
    public ?int $id = null;
    public string $name = '';
    public string $description = '';
    public float $price = 0;
    public int $category_id = 0;
    public int $min_level = 0;
    public ?int $subcategory_id = null;
    public bool $is_active = true;
    public bool $active = true;


    public string $type = 'simple';




    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'active' => 'boolean',
        ];
    }

    public function setProduct(Product $product): void
    {
        $this->id = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->category_id = $product->category_id;
        $this->subcategory_id = $product->subcategory_id;
        $this->active = $product->is_active ?? false;
        $this->min_level = $product->min_level ?? false;

    }
}
