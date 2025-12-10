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

        <!-- Main Content Card -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">



        <!-- Filters Section -->
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">

                <!-- Alter Stock -->
                <button wire:click="createStockProduct"
                        class="inline-flex items-center gap-1.5 px-2 py-1.5 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white text-sm font-medium rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Alter Stock
                </button>


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
                            Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider">
                            Quantidade
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">

                    {{-- Tailwind formating for the items --}}
                    @php
                        $statusClasses = [
                            'available' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200',
                            'reserved'  => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200',
                            'damaged'   => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-200',
                        ];
                    @endphp

                    @forelse($stockProducts as $stockProduct)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded">
                                    {{$loop->iteration}}
                                </span>
                            </td>

                            {{-- STATUS --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $statusClasses[$stockProduct->status] ?? 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200' }}">
                                    {{ __('messages.status.'.$stockProduct->status) ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- QUANTITY --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $statusClasses[$stockProduct->status] ?? 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-200' }}">
                                    {{ $stockProduct->quantity ?? 'N/A' }}
                                </span>
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
                <tfoot class="bg-zinc-50 dark:bg-zinc-900/70 border-t border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <td colspan="2" class="px-6 py-4 text-right text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                            Total
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-zinc-900 dark:text-zinc-100">
                            {{ $stockProducts->sum('quantity') }}
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>

        <!-- Pagination -->
        @if($stockProducts->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400">
                        Showing {{ $stockProducts->firstItem() }} to {{ $stockProducts->lastItem() }} of {{ $stockProducts->total() }} results
                    </div>
                    <div class="flex items-center space-x-2">
                        {{$stockProducts->links() }}
                    </div>
                </div>
            </div>
        @endif

    </div>


    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-2xl w-full max-w-lg border border-zinc-200 dark:border-zinc-700 max-h-[90vh] overflow-y-auto">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-zinc-200 dark:border-zinc-700">
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-white">
                        Alter Stock
                    </h2>
                    <button wire:click="resetForm"
                            class="w-8 h-8 rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 flex items-center justify-center text-zinc-500 dark:text-zinc-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <form wire:submit.prevent="saveStockProduct" class="p-6 space-y-6">

                    <!-- STATUS -->
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                            FROM *
                        </label>
                        <select wire:model.defer="stockProductForm.status.from"
                                class="w-full px-4 py-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Category</option>
                            @foreach(\App\Models\StockProduct::statusOptions() as $key => $label)
                                <option value="{{ $key }}"  @selected($statusFilter === $key) >{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('stockProductForm.status.from')
                            <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror


                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                            TO *
                        </label>
                        <select wire:model.defer="stockProductForm.status.to"
                                class="w-full px-4 py-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Select Category</option>
                            @foreach(\App\Models\StockProduct::statusOptions() as $key => $label)
                                <option value="{{ $key }}"  @selected($statusFilter === $key) >{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('stockProductForm.status.to')
                            <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- QUANTITY -->
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2">
                            QUANTITY *
                        </label>
                        <div class="relative">
                            {{-- <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-zinc-500 dark:text-zinc-400">MZN</span> --}}
                            <input type="number"
                                wire:model.defer="stockProductForm.quantity"
                                step="0.01"
                                class="w-full pl-8 pr-4 py-3 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 placeholder-zinc-500 dark:placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="0">
                        </div>
                        @error('stockProductForm.quantity')
                            <span class="text-red-500 dark:text-red-400 text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- Actions -->
                    <div class="flex gap-3 pt-4">
                        <button type="button"
                                wire:click="resetForm"
                                class="flex-1 px-6 py-3 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 font-medium rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif


</div>
