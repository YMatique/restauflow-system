<?php

namespace App\Livewire\Stock;


use App\Models\Product;
use App\Models\Stock;
use App\Models\StockProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Traits\WithToast;
use Livewire\WithPagination;


#[Layout('components.layouts.app-main')]
class ShowProductStock extends Component
{

    use WithPagination, WithToast;

    public int $perPage = 10;

    // Objetos
    public Stock $stock;
    public Product $product;

    public string $statusFilter = '';

    // View controllers
    public bool $showModal = false;

    // Formulário de StockProduct
    public array $stockProductForm = [
        'stock_id'   => '',
        'product_id' => '',
        'quantity'   => '',
        'status'     => '',
    ];

    protected array $rules = [
        // 'stockProductForm.stock_id'   => 'required|exists:stocks,id',
        // 'stockProductForm.product_id' => 'required|exists:products,id',
        'stockProductForm.quantity'   => 'required|integer|min:0',
        'stockProductForm.status'     => 'required|in:available,reserved,damaged',
    ];

    public function mount(Stock $stock, Product $product)
    {
        $this->stock = $stock;
        $this->product = $product;
    }

    // ----------------------
    // Métodos de formulário
    // ----------------------
    public function createStockProduct(): void
    {
        $this->showModal = true;
    }

    public function saveStockProduct(): void
    {
        $this->validate();

        $data = [
            'stock_id'   => $this->stock->id,
            'product_id' => $this->product->id,
            'status'     => $this->stockProductForm['status'],
            'company_id' => auth()->user()->company_id,
        ];


        $stockProduct = StockProduct::updateOrCreate($data, ['quantity' => $this->stockProductForm['quantity']]);


        // dd($stockProduct ->wasChanged());
        if ($stockProduct->wasChanged()) {

             $this->toastWarning(
                __('messages.toast.success.key'),
                __('messages.toast.success.value', ['verb' => 'update', 'object' => $this->product->name])
            );

            $this->resetForm();

        }

        elseif ($stockProduct->wasRecentlyCreated)
        {
            $this->toastSuccess(
                __('messages.toast.success.key'),
                __('messages.toast.success.value', ['verb' => 'create', 'object' => 'stock-product'])
            );

            $this->resetForm();
          
        }

        else
        {
            $this->toastError(
                __('messages.toast.error.key'),
                __('messages.toast.error.value', ['verb' => 'error', 'object' => 'stock-product'])
            );

        }


    }


    public function resetForm(): void
    {
        $this->reset(['stockProductForm', 'showModal']);
    }

    // ----------------------
    // Renderização
    // ----------------------
    #[Title('Exibindo Produtos')]
    public function render()
    {
        $stockProducts = StockProduct::getByStockProductAndCompany(
            stock: $this->stock,
            product: $this->product,
            companyId: auth()->user()->company_id,
            status: $this->statusFilter,
            perPage: $this->perPage
        );

        return view('livewire.stock.show-product-stock', [
            'stockProducts' => $stockProducts,
            'title' => $this->product->name,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                ['label' => 'Stocks', 'url' => route('restaurant.stocks')],
                [
                    'label' => $this->stock->name,
                    'url' => route('restaurant.stocks.details', ['stock' => $this->stock->id])
                ],
                ['label' => $this->product->name]
            ],
        ]);
    }
}
