<?php

namespace App\Livewire\Stock;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app-main')]
class ShowProductStock extends Component
{
    public  int $perPage = 10;

    public Stock $stock;

    public Product $product;

    public function mount(Stock $stock, Product $product)
    {
        $this->stock = $stock;
        $this->product = $product;
    }

    public function redirectToStockDetails()
    {
        return redirect()->route('restaurant.stocks', [

        ]);
    }

    public function render()
    {

         // Recupera todos os StockProduct do stock e produto para a empresa do usuário
        $stockProducts = StockProduct::getByStockProductAndCompany(
            stock: $this->stock,
            product: $this->product,
            companyId: auth()->user()->company_id,
            perPage: $this->perPage
        );


        return view('livewire.stock.show-product-stock', [
            'stockProducts' => $stockProducts,
            'title' => $this->product->name,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                ['label' => 'Stocks', 'url' => route('restaurant.stocks')],
                ['label' => $this->stock->name, 'url' => route('restaurant.stocks.details',['stock' => $this->stock->id] )],
                ['label' => $this->product->name]

            ],
        ]);
    }
}
