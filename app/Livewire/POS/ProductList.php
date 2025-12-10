<?php

namespace App\Livewire\POS;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;

class ProductList extends Component
{
 public $selectedCategory = null;
    public $searchTerm = '';

    protected $listeners = [
        'categorySelected' => 'selectCategory'
    ];

    public function mount()
    {
        // Seleciona primeira categoria ativa
        $this->selectedCategory = Category::active()
            ->byCompany(auth()->user()->company_id)
            ->first()?->id;
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
    }

    public function addToCart($productId, $quantity = 1)
    {
        $product = Product::find($productId);

        if (!$product) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Produto não encontrado'
            ]);
            return;
        }

        if (!$product->canSell($quantity)) {
            $this->dispatch('toast', [
                'type' => 'error',
                'message' => 'Produto indisponível ou sem stock suficiente'
            ]);
            return;
        }

        // Envia evento para componente pai/carrinho
        $this->dispatch('productAdded', 
            productId: $productId, 
            quantity: $quantity
        );

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => "{$product->name} adicionado ao carrinho"
        ]);
    }

    public function getCategoriesProperty()
    {
        return Category::active()
            ->byCompany(auth()->user()->company_id)
            ->ordered()
            ->get();
    }

    public function getProductsProperty()
    {
        return Product::active()
            ->available()
            ->byCompany(auth()->user()->company_id)
            ->when($this->selectedCategory, fn($q) => 
                $q->where('category_id', $this->selectedCategory)
            )
            ->when($this->searchTerm, fn($q) => 
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
            )
            ->get();
    }
    public function render()
    {
        return view('livewire.p-o-s.product-list',[
            'categories' => $this->categories,
            'products' => $this->products
        ]);
    }
}
