<div>

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                @foreach ($breadcrumb as $item)
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="text-blue-600 hover:underline">{{ $item['label'] }}</a>
                    @else
                        <span>{{ $item['label'] }}</span>
                    @endif
                    @if(!$loop->last)<span class="mx-1">/</span>@endif
                @endforeach
            </p>
        </div>

        <a href="{{ route('restaurant.stocks.details', ['stock' => $stock->id]) }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors cursor-pointer">
                Voltar
        </a>

    </div>

    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md space-y-4">

        <!-- PRODUCT INFO -->
        <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 rounded">
            <p>Categoria: {{ $product->category->name ?? '-' }}</p>
            <p>Subcategoria: {{ $product->subcategory->name ?? '-' }}</p>

            <p>Código: {{ $product->code ?? '-' }}</p>
            <p>Descrição: {{ $product->description ?? '-' }}</p>
        </div>

        <!-- SEARCH & FILTER -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0 md:space-x-4">
            <!-- Rows per Page -->
            <select wire:model.live="perPage" class="border rounded p-2 w-20">
                <option value="5">05</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>

            <!-- Search -->
            <input type="text" wire:model.live="search" placeholder="Search stock items..."
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">

            <!-- Status Alter -->
             <button wire:click="createStockProduct"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors cursor-pointer">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4" />
                </svg>
                Alter Stock
            </button>

        </div>

        <!-- TABLE DE STOCK PRODUCTS -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantidade</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse ($stockProducts as $index => $stockProduct)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ __('messages.status.'.$stockProduct->status) ?? 'N/A' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $stockProduct->quantity ?? 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-4 text-center text-gray-500 dark:text-gray-300">
                            Nenhum item encontrado neste stock.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div class="flex justify-end mt-4">
            {{ $stockProducts->links() }}
        </div>



        <!-- Modal de criação/edição -->
        @if($showModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
                    <h2 class="text-lg font-bold mb-4">Alter Stock</h2>

                    <form wire:submit.prevent="saveStockProduct" class="space-y-4">

                        {{-- STATUS --}}
                        <div>
                            <label class="block text-sm font-medium">Status</label>
                            <select wire:model.defer="stockProductForm.status" class="w-full border rounded p-2">
                                <option value="">-- Select --</option>
                                @foreach(\App\Models\StockProduct::statusOptions() as $key => $label)
                                    <option value="{{ $key }}"  @selected($statusFilter === $key) >{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('stockProductForm.status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- QUANTITY --}}
                        <div>
                            <label class="block text-sm font-medium">Quantity</label>
                            <input type="text" wire:model.defer="stockProductForm.quantity" class="w-full border rounded p-2">
                            @error('stockProductForm.quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>



                        <div class="flex justify-end space-x-2">

                            <button type="button" wire:click="resetForm" class="px-4 py-2 bg-gray-400 text-white rounded cursor-pointer">
                                Cancel
                            </button>

                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded cursor-pointer">
                                Save
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        @endif
        <!-- END BODY -->


    </div>
</div>
