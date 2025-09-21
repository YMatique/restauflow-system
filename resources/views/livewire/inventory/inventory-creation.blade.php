{{-- <div class="space-y-6"> --}}
<div class="flex flex-col h-screen">


    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ $title }}
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

             <button 
                type="button" 
                wire:click="goBack"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 dark:bg-gray-500 dark:hover:bg-gray-600 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
                </svg>
                Voltar
            </button>
    </div>




    <!-- Main Content Card -->
    <div class="flex-1 bg-white dark:bg-zinc-800 rounded-none shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-auto">

        <!-- Filters Section -->
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                
                <!-- selectedStockId -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        {{ __('messages.stock_management.stock_center') }}
                    </label>
                    <select wire:model.live="selectedStockId"
                            class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">-- Select {{ __('messages.stock_management.stock_center') }} --</option>
                        @foreach($stocks as $object)
                            <option value="{{ $object->id }}">{{ $object->name }}</option>
                        @endforeach
                    </select>

                    <!-- Error alert -->
                    @error('selectedStockId')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                <!-- Search -->
                <div class="space-y-2 lg:col-span-2 relative">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Search</label>
                    <div class="relative">
                        <input type="text"
                            wire:model.live="search"
                            placeholder="Search products..."
                            class="w-full pl-10 pr-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 placeholder-zinc-500 dark:placeholder-zinc-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>

                    <!-- Lista de produtos filtrados -->
                    @if($items->isNotEmpty())
                        <ul class="absolute left-0 right-0 mt-1 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-800 max-h-60 overflow-y-auto z-50 shadow-lg">

                            @foreach($items as $product)
                                <li class="px-4 py-2 hover:bg-zinc-100 dark:hover:bg-zinc-700 cursor-pointer"
                                    wire:click="selectProduct({{ $product->id }})">

                                    {{ $product->name }} - {{ $product->slug }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">No products found.</p>
                    @endif
                </div>



                <!-- Status Filter -->
                <div class="space-y-2">
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Categories</label>
                    <select wire:model.live="selectedCategoryId"
                            class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">-- All Types --</option>
                        @foreach(\App\Models\Category::all() as $cat)
                            <option value="{{ $cat->id }}"  @selected($selectedCategoryId === $cat->id) >{{ $cat->name }}</option>
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
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">
                            #
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">
                            Details
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">
                            Quantity
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">
                            Price
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">
                            Subtotal
                        </th>

                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-zinc-600 dark:text-zinc-400 uppercase tracking-wider whitespace-nowrap">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-800">
                    @forelse ($products as $item)
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">{{ $item->name }}</td>
                            <td class="px-6 py-4">{{ $item->description }}</td>


                            <!-- Status dropdown -->
                            <td class="px-6 py-4">
                                <select wire:model.live="productsStatus.{{ $item->id }}"
                                        class="border rounded px-2 py-1 text-sm w-full">
                                    @foreach(\App\Models\StockProduct::statusOptions() as $key => $label)
                                        <option value="{{ $key }}"  @selected("available" === $key) >{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>

                            <!-- Quantity input (smaller width) -->
                           <td class="px-6 py-4">
                                <div class="flex items-center space-x-1">
                                    <!-- Botão diminuir -->
                                    <button type="button"
                                            wire:click="decrementQuantity({{ $item->id }})"
                                            class="px-2 py-1 bg-gray-200 dark:bg-zinc-700 rounded hover:bg-gray-300 dark:hover:bg-zinc-600">
                                        -
                                    </button>

                                    <!-- Input -->
                                    <input type="number"
                                        wire:model.live="productsQuantity.{{ $item->id }}"
                                        class="border rounded px-2 py-1 text-sm w-16 text-center"
                                        min="1">

                                    <!-- Botão aumentar -->
                                    <button type="button"
                                            wire:click="incrementQuantity({{ $item->id }})"
                                            class="px-2 py-1 bg-gray-200 dark:bg-zinc-700 rounded hover:bg-gray-300 dark:hover:bg-zinc-600">
                                        +
                                    </button>
                                </div>
                            </td>

                            <td class="px-6 py-4">{{ number_format($item->price, 2) }}</td>
                            <td class="px-6 py-4">{{ number_format($productsQuantity[$item->id] * $item->price, 2) }}</td>



                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <button wire:click="removeProduct({{ $item->id }})"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-800/50 text-red-600 dark:text-red-400 transition-colors"
                                        title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <svg class="w-12 h-12 text-zinc-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="text-zinc-500 dark:text-zinc-400 font-medium">No product launched!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/70 font-semibold">
                        <td colspan="6" class="px-6 py-4 text-right">Subtotal</td>
                        <td class="px-6 py-4">{{ number_format($this->subtotal, 2) }}</td>
                        <td></td>
                    </tr>
                    <tr class="bg-zinc-50 dark:bg-zinc-900/70 font-semibold">
                        <td colspan="6" class="px-6 py-4 text-right">IVA (17%)</td>
                        <td class="px-6 py-4">{{ number_format($this->iva, 2) }}</td>
                        <td></td>
                    </tr>
                    <tr class="bg-zinc-100 dark:bg-zinc-900 font-bold">
                        <td colspan="6" class="px-6 py-4 text-right">Total</td>
                        <td class="px-6 py-4">{{ number_format($this->total, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>

            </table>
        </div>

        <div class="m-6 flex items-center justify-end space-x-3 border-t pt-4">
            <!-- Save as Draft -->
            <div>
                <button type="button"
                        wire:click="saveDraft"
                        wire:loading.remove
                        wire:target="saveDraft"
                        class="px-4 py-2 bg-gray-200 dark:bg-zinc-700 text-gray-800 dark:text-zinc-200 rounded-lg hover:bg-gray-300 dark:hover:bg-zinc-600 transition-colors">
                    Save as Draft
                </button>
                <button
                    class="px-4 py-2 bg-gray-400 text-white rounded cursor-not-allowed"
                    disabled
                    wire:loading
                    wire:target="saveDraft">
                    Salvando como Rascunho...
                </button>
            </div>

            <!-- Save -->
            <div>
                <button
                    wire:click="save"
                    wire:loading.remove
                    wire:target="save"
                    class="px-4 py-2 bg-blue-600 text-white rounded">
                    Salvar
                </button>
                <button
                    class="px-4 py-2 bg-gray-400 text-white rounded cursor-not-allowed"
                    disabled
                    wire:loading
                    wire:target="save">
                    Salvando...
                </button>
            </div>
        </div>



    </div>

</div>
