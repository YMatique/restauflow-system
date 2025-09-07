<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Traits\WithToast;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManagement extends Component
{
    use WithPagination, WithToast;


    protected string $layout = 'layouts.app';

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $viewMode = 'grid'; // grid or table

    public $showModal = false;
    public $editingProduct = null;

    public $productForm = [
        'name' => '',
        'category_id' => '',
        'description' => '',
        'price' => '',
        'type' => 'simple',
        'stock_quantity' => '',
        'min_level' => '',
        'barcode' => '',
        'track_stock' => true,
        'is_available' => true,
        'is_active' => true
    ];

    protected $rules = [
        'productForm.name' => 'required|string|max:255',
        'productForm.category_id' => 'required|exists:categories,id',
        'productForm.price' => 'required|numeric|min:0',
        'productForm.type' => 'required|in:simple,composed',
        'productForm.stock_quantity' => 'nullable|numeric|min:0',
        'productForm.min_level' => 'nullable|numeric|min:0',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function createProduct()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function saveProduct()
    {
        $this->validate();

        $data = array_merge($this->productForm, [
            'company_id' => auth()->user()->company_id
        ]);

        if ($this->editingProduct) {
            $this->editingProduct->update($data);
            $message = 'Produto atualizado com sucesso!';
        } else {
            Product::create($data);
            $message = 'Produto criado com sucesso!';
        }

        $this->resetForm();

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    public function editProduct(Product $product)
    {
        $this->editingProduct = $product;
        $this->productForm = $product->only([
            'name', 'category_id', 'description', 'price', 'type',
            'stock_quantity', 'min_level', 'barcode', 'track_stock',
            'is_available', 'is_active'
        ]);
        $this->showModal = true;
    }

    public function deleteProduct(Product $product)
    {
        $product->update(['is_active' => false]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Produto removido com sucesso!'
        ]);
    }

    public function resetForm()
    {
        $this->productForm = [
            'name' => '',
            'category_id' => '',
            'description' => '',
            'price' => '',
            'type' => 'simple',
            'stock_quantity' => '',
            'min_level' => '',
            'barcode' => '',
            'track_stock' => true,
            'is_available' => true,
            'is_active' => true
        ];
        $this->editingProduct = null;
        $this->showModal = false;
    }

    public function render()
    {
        $products = Product::query()
            ->byCompany(auth()->user()->company_id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->categoryFilter, function ($query) {
                $query->where('category_id', $this->categoryFilter);
            })
            ->when($this->statusFilter, function ($query) {
                match ($this->statusFilter) {
                    'available' => $query->available(),
                    'unavailable' => $query->where('is_available', false),
                    'low-stock' => $query->lowStock(),
                    default => $query
                };
            })
            ->with(['category'])
            ->paginate(10);

        return view('livewire.products.product-management', [
            'products' => $products,
            'categories' => Category::active()->byCompany(auth()->user()->company_id)->get(),
            'subcategories' => [],//Subcategory::active()->byCompany(auth()->user()->company_id)->get(),
            'title' => 'Gestão de Produtos',
            'breadcrumb' => 'Dashboard > Produtos'
        ]);
    }
}
