<div class="space-y-6">
     <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $stock->name }}
            </h1>
            <nav class="text-sm text-zinc-500 dark:text-zinc-400">
                @foreach ($breadcrumb as $item)
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="text-zinc-700 dark:text-zinc-300">{{ $item['label'] }}</span>
                    @endif
                    @if(!$loop->last)
                        <span class="mx-2 text-zinc-400">/</span>
                    @endif
                @endforeach
            </nav>
        </div>

        <a href="{{ route('restaurant.stocks') }}"
            class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
            </svg>
            Voltar
        </a>
    </div>


    @php
        $totalProducts = $products->count();

        // Valor total do stock (available * price)
        $stockValue = $products->sum(fn($p) => $p->available * ($p->price ?? 0));

        // Produtos abaixo do stock mínimo
        $lowStockProducts = $products->filter(
            fn($p) => $p->available < ($p->min_stock ?? 0)
        );

        // Valor da rutura (quantidade em falta * preço)
        $stockOutValue = $lowStockProducts->sum(function ($p) {
            $missing = max(0, ($p->min_stock ?? 0) - $p->available);
            return $missing * ($p->price ?? 0);
        });
    @endphp
    <!-- Summary Section -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">

        <!-- Total Products -->
        <div class="p-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Total de Produtos</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ $totalProducts }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    📦
                </div>
            </div>
        </div>

        <!-- Stock Value -->
        <div class="p-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Valor do Stock</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ number_format($stockValue, 2, ',', '.') }} MT
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    💰
                </div>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="p-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Abaixo do Stock</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ $lowStockProducts->count() }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                    ⚠️
                </div>
            </div>
        </div>

        <!-- Stock Out Value -->
        <div class="p-6 bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">Valor da Rutura</p>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                        {{ number_format($stockOutValue, 2, ',', '.') }} MT
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                    ❌
                </div>
            </div>
        </div>

    </div>


    <!-- Main Content Card -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">

        <!-- Filters Section -->
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <!-- Per Page -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Rows</label>
                    <select wire:model.live="perPage"
                            class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="05">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>

                <!-- Search -->
                <div class="space-y-2 lg:col-span-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Search</label>
                    <div class="relative">
                        <input type="text"
                               wire:model.live="search"
                               placeholder="Search stocks..."
                               class="w-full pl-10 pr-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 placeholder-zinc-500 dark:placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>


                <!-- Status Filter -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</label>
                    <select wire:model.live="statusFilter"
                            class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">All Status</option>
                        <@foreach(App\Models\Stock::statusOptions() as $key => $value)
                            <option value="{{$key}}" @selected($statusFilter === $key)> {{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/70 border-b border-zinc-200 dark:border-zinc-700">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                            #
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                            Total
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                            Available
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                            Reserved
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                            Damaged
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($products as $index => $product)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded">
                                    {{$loop->iteration}}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    {{ $product->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-blue-900/30 text-green-800 dark:text-green-200">
                                    {{ $product->total }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                    {{ $product->available }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                    {{ $product->reserved }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">

                                    {{ $product->damaged }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- View -->
                                    <button
                                        wire:click="redirectToStockProduct({{ $stock->id}}, {{ $product->id }})"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-800/50 text-blue-600 dark:text-blue-400 transition-colors"
                                       title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-12 h-12 text-zinc-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-zinc-500 dark:text-zinc-400 font-medium">
                                        {{ __('messages.nothing_found', ['record' => __('messages.product')]) }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/70">
                    <tr class="font-semibold">
                        <!-- # -->
                        <td class="px-6 py-4 text-sm text-zinc-700 dark:text-zinc-300">
                            —
                        </td>

                        <!-- Label -->
                        <td class="px-6 py-4 text-sm text-zinc-900 dark:text-zinc-100 uppercase">
                            Totais
                        </td>

                        <!-- Total -->
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200">
                                {{ $products->sum('total') }}
                            </span>
                        </td>

                        <!-- Available -->
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200">
                                {{ $products->sum('available') }}
                            </span>
                        </td>

                        <!-- Reserved -->
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                                {{ $products->sum('reserved') }}
                            </span>
                        </td>

                        <!-- Damaged -->
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200">
                                {{ $products->sum('damaged') }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-center text-sm text-zinc-500">
                            —
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} results
                    </div>
                    <div class="flex items-center space-x-2">
                        {{$products->links() }}
                    </div>
                </div>
            </div>
        @endif

    </div>

</div>
