<div>

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $stock->name }}
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                @foreach ($breadcrumb as $item)
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="text-blue-600 hover:underline">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span>{{ $item['label'] }}</span>
                    @endif

                    @if(!$loop->last)
                        <span class="mx-1">/</span>
                    @endif
                @endforeach


            </p>
        </div>
        <a href="{{ route('restaurant.stocks') }}"
            class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors cursor-pointer">
                Voltar
            </a>
        </div>


    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md space-y-4">


        <!-- SEARCH & FILTER -->
        <div
            class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 space-y-2 md:space-y-0 md:space-x-4">

            <!-- Rows per Page -->
            <select wire:model.live="perPage" class="border rounded p-2 w-20">
                <option value="5">05</option>
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>

            <!-- Search -->
            <input type="text" wire:model.live="search" placeholder="Search products..."
                class="px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-full md:w-1/3">



            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="border rounded p-2">
                <option value="">-- All Status --</option>
                <option value="active">  {{ __('messages.status.active') }}</option>
                <option value="inactive"> {{ __('messages.status.inactive') }}</option>
                <option value="maintenance">{{ __('messages.status.maintenance') }}</option>
            </select>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Available</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reserved</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Damaged</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>


                    </tr>
                </thead>

                 <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($products as $product)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap">{{ $loop->iteration }}</td>

                        <td class="px-4 py-2 whitespace-nowrap">{{ $product->name }}</td>

                        <td class="px-4 py-2 whitespace-nowrap">
                            {{ $product->total }}
                        </td>

                         <td class="px-4 py-2 whitespace-nowrap">
                            {{ $product->available }}
                        </td>


                         <td class="px-4 py-2 whitespace-nowrap">
                            {{ $product->reserved }}
                        </td>

                           <td class="px-4 py-2 whitespace-nowrap">
                            {{ $product->damaged }}
                        </td>

                        <td class="px-4 py-2 whitespace-nowrap space-x-2">

                            <button wire:click="redirectToStockProduct({{ $stock->id}}, {{ $product->id }})"
                                    class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer">
                                👁️
                            </button>

                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500 dark:text-gray-300">
                            {{ __('messages.nothing_found', ['record' => __('messages.product_management.key')]) }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
        <!-- END TABLE -->


        <!-- PAGINATION -->
        <div class="flex justify-end mt-4">
            {{ $products->links() }}
        </div>

    </div>

</div>
