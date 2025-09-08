<div>
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('messages.product_management.title') }}</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $breadcrumb }}</p>
        </div>

        <button
            wire:click="createProduct"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{ __('messages.dashboard.products') }}
        </button>

    </div>

    <!-- BODY -->

    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md space-y-4">



        <!-- SEARCH & FILTER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0 md:space-x-4">

             <!-- Rows per Page -->
            <select wire:model.live="perPage" class="border rounded p-2 w-15">
                <option value="05">05</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>

            <input type="text" wire:model.live="search" placeholder="Search products..."
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">

            <select wire:model.live="categoryFilter" class="border rounded p-2">
                <option value="">-- All Categories --</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter" class="border rounded p-2">
                <option value="">-- All Status --</option>
                <option value="available">Available</option>
                <option value="unavailable">Unavailable</option>
                <option value="low-stock">Low Stock</option>
            </select>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#Code</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($products as $index => $product)
                        <tr>
                            {{-- <td class="px-4 py-2 whitespace-nowrap">{{ $index + $products->firstItem() }}</td> --}}
                            <td class="px-4 py-2 whitespace-nowrap">{{ $product->code }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $product->name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $product->category?->name ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $product->stock_quantity }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">${{ number_format($product->price, 2) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap space-x-2">
                                <button wire:click="editProduct({{ $product->id }})"
                                    class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</button>
                                <button wire:click="deleteProduct({{ $product->id }})"
                                    class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500 dark:text-gray-300">
                               {{
                                    __('messages.nothing_found',
                                    ['record' => __('messages.product')])
                                }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-end mt-4 space-x-2">
            {{ $products->links() }}
        </div>

    </div>

    <!-- Modal de criação/edição -->
    @if($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
                <h2 class="text-lg font-bold mb-4">{{ $editingProduct ? 'Edit Product' : 'New Product' }}</h2>

                <form wire:submit.prevent="saveProduct" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium">Name</label>
                        <input type="text" wire:model.defer="productForm.name" class="w-full border rounded p-2">
                        @error('productForm.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Price</label>
                        <input type="number" wire:model.defer="productForm.price" step="0.01" class="w-full border rounded p-2">
                        @error('productForm.price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                   <div>
                        <label class="block text-sm font-medium">Category</label>
                        <select wire:model.live="productForm.category_id" class="w-full border rounded p-2">
                            <option value="">-- Select --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('productForm.category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Subcategory</label>
                        <select wire:model.defer="productForm.subcategory_id" class="w-full border rounded p-2">
                            <option value="">-- Select --</option>
                            @foreach($subcategories as $subcategory)
                                <option value="{{ $subcategory->id }}">{{ $subcategory->name }}</option>
                            @endforeach
                        </select>
                        @error('productForm.subcategory_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>


                    <div class="flex justify-end space-x-2">
                        <button type="button" wire:click="resetForm" class="px-4 py-2 bg-gray-400 text-white rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                            {{ $editingProduct ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <!-- END BODY -->
</div>
