<div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md space-y-4">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $breadcrumb }}</p>
        </div>
        <button wire:click="createProduct" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            {{  __('messages.dashboard.stoks') }}
        </button>
    </div>

    <!-- SEARCH & FILTER -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0 md:space-x-4">
        <!-- Rows per Page -->
        <select wire:model.live="perPage" class="border rounded p-2 w-15">
            <option value="5">05</option>
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
        </select>

        <input type="text" wire:model.live="search" placeholder="Search products..." class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">

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

    <!-- TABLE -->
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
                @forelse($stocks as $stock)
                <tr>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $product->code }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $product->name }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $product->category?->name ?? '-' }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">{{ $product->stock_quantity }}</td>
                    <td class="px-4 py-2 whitespace-nowrap">${{ number_format($product->price, 2) }}</td>
                    <td class="px-4 py-2 whitespace-nowrap space-x-2">
                        <button wire:click="editProduct({{ $product->id }})" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</button>
                        <button wire:click="deleteProduct({{ $product->id }})" class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-4 text-center text-gray-500 dark:text-gray-300">
                        {{ __('messages.nothing_found', ['record' => __('messages.product')]) }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PAGINATION -->
    <div class="flex justify-end mt-4">
        {{-- {{ $products->links() }} --}}
    </div>

    <!-- MODAL -->
    {{-- @if($showModal)
        <livewire:stock.product-modal :categories="$categories" :subcategories="$subcategories" :editingProduct="$editingProduct" />
    @endif --}}

</div>
