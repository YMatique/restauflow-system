<?php

namespace App\Livewire\POS;

use App\Models\Category;
use App\Models\Product;
use App\Models\Table;
use Livewire\Component;

class POSComponent extends Component
{
    protected string $layout = 'layouts.pos';
    
    public $currentTable = null;
    public $selectedCategory = null;
    public $cart = [];

        public function mount()
    {
        $this->selectedCategory = Category::active()->first()?->id;
    }
    
    public function selectTable($tableId)
    {
        $this->currentTable = Table::find($tableId);
        $this->dispatch('table-selected', $this->currentTable);
    }
    
    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }
    
    public function addToCart($productId, $quantity = 1)
    {
        $product = Product::find($productId);
        
        if (!$product->canSell($quantity)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Produto indisponível ou sem stock suficiente'
            ]);
            return;
        }
        
        $cartKey = $productId;
        
        if (isset($this->cart[$cartKey])) {
            $this->cart[$cartKey]['quantity'] += $quantity;
        } else {
            $this->cart[$cartKey] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $product->price,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity
            ];
        }
        
        $this->dispatch('cart-updated');
    }
    
    public function render()
    {
        return view('livewire.p-o-s.p-o-s-component',[
            'categories' => Category::active()->ordered()->get(),
            'products' => Product::active()->available()
                ->when($this->selectedCategory, fn($q) => $q->where('category_id', $this->selectedCategory))
                ->get(),
            'tables' => Table::active()->get(),
            'cartTotal' => collect($this->cart)->sum('total_price')
        ]);
    }
}
