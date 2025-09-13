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

            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="border rounded p-2">
                <option value="">-- Todos Status --</option>
                <option value="available">Disponível</option>
                <option value="reserved">Reservado</option>
                <option value="damaged">Danificado</option>
            </select>
        </div>

        <!-- TABLE DE STOCK PRODUCTS -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantidade</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse ($stockProducts as $index => $stockProduct)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $stockProduct->status ?? 'N/A' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $stockProduct->quantity ?? 'N/A' }}</td>
                        <td class="px-4 py-2 whitespace-nowrap space-x-2">
                            <button wire:click="editStockProduct({{ $stockProduct->id }})"
                                class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600">
                                Editar
                            </button>
                            <button wire:click="deleteStockProduct({{ $stockProduct->id }})"
                                class="px-2 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                Excluir
                            </button>
                        </td>
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

    </div>
</div>
