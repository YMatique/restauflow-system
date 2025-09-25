<?php

namespace App\Livewire\Inventory;

use App\Models\DocType;
use App\Models\Product;
use App\Models\Inventory;
use App\Traits\WithToast;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app-main')]
class InventoryCreation extends Component
{

    use WithToast;

    public function goBack()
    {
        return $this->js('history.back()');
    }


    // VIEW
    public $search;
    public $stocks;
    public $companyId;
    public $selectedStockId;
    public $selectedCategoryId;

    // ITEMS FOR THE SEARCH
    public Collection $items;

    // INVENTORY PRODUCTS
    public Collection $products;

    public $productsQuantity = []; // Armazena quantidade de cada produto
    public $productsStatus = [];

    public function mount()
    {
        $this->companyId = auth()->user()->company_id;
        $this->stocks = \App\Models\Stock::where('company_id', $this->companyId)->get();
        $this->items = new Collection();
        $this->products = new Collection();
    }

    public function updatedSearch($value)
    {
        $this->items = Product::with('category')
            ->where('company_id', $this->companyId)
            ->where(function($query) use ($value) {
                $query->where('name', 'like', '%' . $value . '%')
                      ->orWhere('slug', 'like', '%' . $value . '%')
                      ->orWhere('description', 'like', '%' . $value . '%');
            })
            ->when($this->selectedCategoryId, function($query) {
                $query->whereHas('categories', fn($q) => $q->where('id', $this->selectedCategoryId));
            })
            ->get();
    }

    public function selectProduct(Product $product)
    {
        if (!$this->products->contains('id', $product->id)) {
            $this->products->push($product);
            $this->productsQuantity[$product->id] = 1;
            $this->productsStatus[$product->id] = 'available'; // inicializa status padrão
        }

        $this->reset('search');
        $this->items = new Collection();
    }

    public function removeProduct(Product $product)
    {
        $this->products = $this->products->reject(fn($item) => $item->id === $product->id)->values();
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

    // TABLE FOOT
    public function getSubtotalProperty(): float
    {
        return $this->products->sum(fn($item) => ($this->productsQuantity[$item->id] ?? 1) * $item->price);
    }

    public function getIvaProperty(): float
    {
        return $this->subtotal * 0.17; // 17%
    }

    public function getTotalProperty(): float
    {
        return $this->subtotal + $this->iva;
    }

    public function saveDraft()
    {
        // Implementar lógica de rascunho
    }

    public function save()
    {
        $user = auth()->user();

        $this->validate([
            'selectedStockId' => 'required'
        ]);

        try {

            DB::transaction(function () use ($user) {

                // Criar ou atualizar DocType
                $docType = DocType::updateOrCreate(
                    [
                        'company_id' => $user->company_id,
                        'namespace'  => Inventory::class,
                        'sigla'      => Inventory::SIGLA,
                        'description'=> Inventory::DESCRIPTION,
                    ]
                );

                // Calcular subtotal e total
                $subtotal = $this->subtotal;
                $total = $this->total;

                $inventory = Inventory::create([
                    'company_id' => $user->company_id,
                    'stock_id'  => $this->selectedStockId,
                    'user_id'    => $user->id,
                    'reference'  => 'INV/' . str_pad($docType->nextNumerator(), 4, '0', STR_PAD_LEFT) . '-' . now()->format('Ymd'),
                    'subtotal'   => $subtotal,
                    'total'      => $total,
                    'status'     => 'finalized', // ou 'draft' se for salvar rascunho
                ]);


                // Opcional: salvar produtos relacionados
                foreach ($this->products as $product) {
                    $inventory->items()->create([
                        'product_id'=> $product->id,
                        'quantity' => $this->productsQuantity[$product->id] ?? 1,
                        'status'   => $this->productsStatus[$product->id] ?? 'available',
                        'price'    => $product->price,
                        'subtotal' => $product->price * ($this->productsQuantity[$product->id]?? 1),
                        'batch_number' => null, // ou fornecer valor se houver
                        'expiry_date'  => null, // ou fornecer valor se houver
                    ]);
                }

            });
            
            $this->mount();       // Call the mount method again

            $this->toastSuccess(
                    __('messages.toast.success.key'),
                    __('messages.toast.success.value', ['verb' => 'create', 'object' => 'Inventory'])
            );

            
        } 

        catch (\Exception $e)
        {
            $this->toastError(
                __('messages.toast.error.key'),
                __('messages.toast.error.value', ['verb' => 'create', 'object' => 'Inventory'])
            );
        }

    }

    #[Title('Criar - Inventório')]
    public function render()
    {
        return view('livewire.inventory.inventory-creation', [
            'title'      => 'Inventário',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('restaurant.dashboard')],
                ['label' => __('messages.inventory_management.key'), 'url' => route('restaurant.inventory')],
                ['label' => __('messages.modal.create')],
            ],
        ]);
    }
}
