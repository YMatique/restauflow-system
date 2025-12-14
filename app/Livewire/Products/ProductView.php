<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\Product;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app-main')]

class ProductView extends Component
{
    public $product;

    // Recebe o ID do produto na URL
    public function mount(Product $product)
    {
        $this->product = $product->load(['category', 'subcategory']);
    }

    public function render()
    {
        return view('livewire.products.product-view');
    }
}
