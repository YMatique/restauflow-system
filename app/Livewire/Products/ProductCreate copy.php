<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app-main')]
class ProductCreate extends Component
{
    public array $form = [
        'category_id'     => '',
        'subcategory_id'  => null,
        'code'            => '',
        'name'            => '',
        'description'     => '',
        'price'           => 0,
        'cost'            => 0,
        'type'            => 'simple',
        'stock_quantity'  => 0,
        'min_level'       => 0,
        'cost_per_unit'   => 0,
        'track_stock'     => true,
        'is_available'    => true,
        'is_active'       => true,
        'is_featured'     => false,
    ];

    public function rules(): array
    {
        return [
            'form.category_id'    => 'required|exists:categories,id',
            'form.subcategory_id' => 'required|exists:subcategories,id',
            'form.code'           => 'required|string|unique:products,code',
            'form.name'           => 'required|string|max:255',
            'form.price'          => 'required|numeric|min:0',
            'form.cost'           => 'nullable|numeric|min:0',
            'form.type'           => 'required|in:simple,composed',

            'form.stock_quantity' => 'nullable|numeric|min:0',
            'form.min_level'      => 'required|numeric|min:0',
            'form.cost_per_unit'  => 'nullable|numeric|min:0',
        ];
    }

    public function save()
    {
        // 1️⃣ Valida os campos do formulário
        $this->validate();

        // 2️⃣ Prepara os dados para criar o produto
        $productData = array_merge($this->form, [
            'slug'       => Str::slug($this->form['name']),
            'company_id' => Auth::user()->company_id,
        ]);

        // dd($productData);

        // 3️⃣ Cria o produto no banco
        Product::create($productData);

        // 4️⃣ (Opcional) Redireciona com mensagem de sucesso
        return redirect()->route('restaurant.products')
            ->with('success', 'Produto criado com sucesso!');
    }


    public function render()
    {
        return view('livewire.products.product-create', [
            'categories'    => Category::all(),
            'subcategories' => Subcategory::where('category_id', $this->form['category_id'])->get(),
        ]);
    }
}
