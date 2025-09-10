<div>

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $title }} ➡️
                {{ \App\Models\Stock::find($selectedStockId)->name }}
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
        <button wire:click="createStock"
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors cursor-pointer">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v16m8-8H4" />
            </svg>
            {{ __('messages.dashboard.stoks') }}
        </button>
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

                            <button wire:click="editStock({{ $product->id }})"
                                class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 cursor-pointer" title="{{ __('messages.forms.title.edit') }}">
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

        <!-- Modal de criação/edição -->
        @if($showModal)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
                    <h2 class="text-lg font-bold mb-4">{{ $editingStock ? 'Edit Stock' : 'New Stock' }}</h2>

                        {{-- <form wire:submit.prevent="{{ $editingStock ? 'updateStock(stockForm.id)' :'saveStock' }}" class="space-y-4"> --}}
                        <form wire:submit.prevent="{{ $editingStock ? 'updateStock(' . $stockForm['id'] . ')' : 'saveStock' }}" class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium">Name</label>
                            <input type="text" wire:model.defer="stockForm.name" class="w-full border rounded p-2">
                            @error('stockForm.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Observações</label>
                            <textarea wire:model.defer="stockForm.notes"
                                    rows="3"
                                    class="w-full border rounded p-2"></textarea>
                            @error('stockForm.notes')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    <div>
                            <label class="block text-sm font-medium">Status</label>
                            <select wire:model.defer="stockForm.status" class="w-full border rounded p-2">
                                <option value="">-- Select --</option>
                                @foreach($statusDropDown as $key => $category)
                                    <option value="{{$key}}" {{ $key == 'active' ? 'selected' : '' }}>
                                        {{ $category }}
                                    </option>                            @endforeach
                            </select>
                            @error('stockForm.status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>


                        <div class="flex justify-end space-x-2">
                            <button type="button" wire:click="resetForm" class="px-4 py-2 bg-gray-400 text-white rounded cursor-pointer">Cancel</button>
                           <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded cursor-pointer">
                                {{ $editingStock ? 'Update' : 'Save' }}
                            </button>

                        </div>
                    </form>
                </div>
            </div>
        @endif
        <!-- END BODY -->
    </div>

</div>
