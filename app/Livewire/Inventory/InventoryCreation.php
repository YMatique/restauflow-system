<?php

namespace App\Livewire\Inventory;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


#[Layout('components.layouts.app-main')]
class InventoryCreation extends Component
{

    //VIEW
    public $search;
    public  $stocks;
    public $companyId;
    public $selectedCategoryId;


    //ITEMS FOR THE SEARCH
    public Collection $items;

    //INVECTORY PRODUCTS
    public Collection $products;

    public $productsQuantity = []; // armazena quantidade de cada produto
    public $productsStatus = [];



    public function mount(){
        $this->companyId = auth()->user()->company_id;
        $this->stocks = \App\Models\Stock::where('company_id', $this->companyId)->get();
        $this->items = new Collection();
        $this->products = new Collection();

    }

    public function updatedSearch($value)
    {
        $this->items = Product::with(['category'])
            ->where('company_id', $this->companyId)
            ->where(function($query) use ($value) {
                $query->where('name', 'like', '%' . $value . '%')
                    ->orWhere('slug', 'like', '%' . $value . '%')
                    ->orWhere('description', 'like', '%' . $value . '%');
            })
            ->when($this->selectedCategoryId, function($query) {
                $query->whereHas('categories', function ($q) {
                    $q->where('id', $this->selectedCategoryId);
                });
            })->get();
    }


    public function selectProduct(Product $product){

        if (! $this->products->contains('id', $product->id)) {
            $this->products->push($product);
            $this->productsQuantity[$product->id] = 1;
            // $this->productsStatus[$product->id];
        }
        $this->reset(['search',]);
        $this->items = new Collection();
    }

    public function removeProduct(Product $product)
    {
        $this->products = $this->products->reject(function ($item) use ($product) {
            return $item->id === $product->id;
        })->values(); // ->values() reindexa a coleção

        // Remove a quantidade correspondente
        unset($this->productsQuantity[$product->id]);
        unset($this->productsStatus[$product->id]);

    }


    public function incrementQuantity($productId)
    {
        $this->productsQuantity[$productId]++;
    }

    public function decrementQuantity($productId)
    {
        if ($this->productsQuantity[$productId] > 1) {
            $this->productsQuantity[$productId]--;
        }
    }


    //TABLE FOOT

    public function getSubtotalProperty()
    {
        $total = 0;

        foreach ($this->products as $item) {
            $qty = $this->productsQuantity[$item->id] ?? 1;
            $total += $qty * $item->price;
        }

        return $total;
    }

    public function getIvaProperty()
    {
        $ivaPercent = 0.17; // 17% por exemplo
        return $this->subtotal * $ivaPercent;
    }

    public function getTotalProperty()
    {
        return $this->subtotal + $this->iva;
    }

    public function saveDraft()
    {
        $this->stockForm['status'] = 'draft';
        $this->saveStock();
    }


    public function save(){
        
    }



    #[Title('Criar - Inventorio')]
    public function render()
    {
        return view('livewire.inventory.inventory-creation',[
            'title'      => 'Inventário',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                ['label' =>  __('messages.inventory_management.key'), 'url' => route('restaurant.inventory') ],
                ['label' => __('messages.modal.create'), ],
            ],
        ]);
    }
}
