<?php

namespace App\Livewire\Stock;

use App\Models\Stock;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Layout('components.layouts.app-main')]
class StockDetails extends Component
{

    // Pagination
    public $perPage = 10;

    public Stock $stock;

    public string $statusFilter = '';


    public function mount(Stock $stock){//Injeção do stock atraves da rota
        $this->stock = $stock;
    }

    public function redirectToStockProduct($stockId, $productId)
    {
        return redirect()->route('restaurant.stock.products', [
            'stock' => $stockId,
            'product' => $productId
        ]);
    }


    #[Title('Testando o layout')]
    public function render()
    {

        $companyId = auth()->user()->company_id;


        $products = Stock::getProductsSummary(
                companyId: $companyId,
                perPage:  $this->perPage,
                stockId: $this->stock->id, // opcional - caso contrario leva tudo
        );

        return view('livewire.stock.stock-details',[
            'products'   => $products,
            'title'      => __('messages.stock_management.stock_detail'),
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                ['label' => 'Stocks', 'url' => route('restaurant.stocks')],
                ['label' => $this->stock->name]
            ],
        ]);
    }
}
