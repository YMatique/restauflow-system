<?php

namespace App\Livewire\POS;

use App\Models\Product;
use Livewire\Component;

class ProductsGrid extends Component
{
    // Apenas escuta a mudança de categoria
    public $selectedCategory = null;
    public $currentTableId = null; 

    protected $listeners = [
        'categorySelected' => 'updateCategory'
    ];

    public function updateCategory($categoryId): void
    {
        $this->selectedCategory = $categoryId;
    }

    // MODIFICADO: Disparo de evento DIRECIONADO para o OrderSummary
    public function addToCart(int $productId, int $quantity = 1): void
    {
        // dd('as');
        $this->dispatch('productAddedToCart', productId: $productId, quantity: $quantity,  tableId: $this->currentTableId);
            //  ->to(OrderSummary::class); 
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
        return view('livewire.p-o-s.products-grid',[
            'products' => $this->products 
        ]);
    }
}
