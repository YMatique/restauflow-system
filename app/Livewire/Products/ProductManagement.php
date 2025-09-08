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

    public $subcategories = [];

    public $categoryFilter = '';

    public $statusFilter = '';

    public $viewMode = 'grid'; // grid or table

    public $showModal = false;

    public $editingProduct = null;

    public $perPage = 10; //PAgination

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


    public function createProduct()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function saveProduct()
    {

        dd($this->productForm);
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

    public function updatedProductFormCategoryId($categoryId)
    {
        if ($categoryId) {
            $this->subcategories = \App\Models\Subcategory::where('category_id', $categoryId)
                                        ->where('is_active', true)
                                        ->get();
        } else {
            $this->subcategories = [];
        }

        // Limpar a seleção de subcategoria ao mudar de categoria
        $this->productForm['subcategory_id'] = null;
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
            ->paginate($this->perPage);

        return view('livewire.products.product-management', [
            'products' => $products,
            'categories' => Category::active()->byCompany(auth()->user()->company_id)->get(),
            'title' => 'Gestão de Produtos',
            'breadcrumb' => 'Dashboard > Produtos'
        ]);
    }
}
