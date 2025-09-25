<?php

namespace App\Livewire\POS;

use App\Models\Product;
use Livewire\Component;

class ProductsGrid extends Component
{
      public $selectedCategory = null;

    protected $listeners = [
        'categorySelected' => 'updateCategory'
    ];

    public function mount($selectedCategory = null)
    {
        $this->selectedCategory = $selectedCategory;
    }

    public function updateCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function addToCart($productId, $quantity = 1)
    {
        $this->dispatch('productAdded', productId: $productId, quantity: $quantity);
    }

    public function getProductsProperty()
    {
        $query = Product::where('company_id', auth()->user()->company_id)
            ->where('is_active', true)
            ->where('is_available', true);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        return $query->orderBy('name')->get();
    }
    public function render()
    {
        return view('livewire.p-o-s.products-grid', [
            'products' => $this->products
        ]);
    }
}
