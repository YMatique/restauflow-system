<?php

namespace App\Livewire\Products;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Forms\ProductForm; // Import the Form Object

#[Layout('components.layouts.app-main')]
class ProductCreateOrEdit extends Component
{
    // 1. Inject the Livewire Form Object
    public ProductForm $form;

    // Define the product property for clarity and type-hinting
    public ?Product $product = null; // Existing product (optional)

    /**
     * Mount for dependency injection of Product (for editing) or setting up a new product.
     * Uses Route Model Binding (?Product $product = null).
     */
    public function mount($productId = null): void
    {
        // Check if an ID was passed, indicating an edit operation
        if ($productId) {
            // Manually find the product, aborting with a 404 if it doesn't exist
            $product = Product::findOrFail($productId);

            $this->product = $product->load(['category', 'subcategory']);

            // Populate the form object with the product data
            $this->form->setProduct($this->product);
        } else {
            // For creation, ensure we start with a clean form
            $this->form->reset();
        }
    }

    /**
     * Handles saving/updating the product.
     */
    public function save()
    {
        // 3. Validation is now handled inside the form object
        $this->form->validate();

        // Prepare data for creation/update
        $data = $this->form->all();
        $data['slug'] = Str::slug($data['name']);

        if ($this->product) {
            // Edit
            $this->product->update($data);
            session()->flash('success', 'Produto atualizado com sucesso!');
        } else {
            // Create
            Product::create([
                ...$data,
                // Assign company_id from the authenticated user
                'company_id' => Auth::user()->company_id,
            ]);
            // 4. Reset the form fields after successful creation (optional but good practice)
            $this->form->reset();
            session()->flash('success', 'Produto criado com sucesso!');
        }

        return redirect()->route('restaurant.products');
    }

    /**
     * Renders the component view.
     */
    public function render()
    {
        return view('livewire.products.product-create-or-edit', [
            // Only fetch necessary columns for performance
            'categories' => Category::all(['id', 'name']),
            'subcategories' => Subcategory::all(['id', 'name']),
        ]);
    }
}
