{{-- resources/views/livewire/pos/pos-component.blade.php --}}
<div class="flex h-full w-full">
    <!-- Categories Sidebar -->
    <div class="w-48 bg-white border-r-2 border-gray-200 p-4 overflow-y-auto scrollbar-thin">
        <div class="space-y-2">
            @forelse($categories as $category)
                <button 
                    wire:click="selectCategory({{ $category->id }})"
                    class="w-full flex flex-col items-center p-3 rounded-xl transition-all duration-200 {{ $selectedCategory === $category->id ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}"
                >
                    <div class="text-2xl mb-2">{{ $category->emoji }}</div>
                    <div class="text-xs font-medium text-center">{{ $category->name }}</div>
                </button>
            @empty
                <div class="text-center py-8">
                    <div class="text-3xl mb-2">📋</div>
                    <p class="text-gray-500 text-sm">Nenhuma categoria</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="flex-1 p-6 bg-gray-50 overflow-y-auto scrollbar-thin">
        @if($selectedCategory)
            <div class="grid grid-cols-3 gap-4">
                @forelse($products as $product)
                    <div 
                        wire:click="addToCart({{ $product->id }})"
                        class="bg-white rounded-xl p-4 cursor-pointer transition-all duration-200 hover:shadow-lg hover:-translate-y-1 {{ !$product->canSell() ? 'opacity-50 cursor-not-allowed' : '' }}"
                        title="{{ $product->description }}"
                    >
                        <!-- Stock Indicator -->
                        <div class="flex justify-between items-start mb-3">
                            <div class="w-3 h-3 rounded-full {{ $product->getStockStatus() === 'in_stock' ? 'bg-green-500' : ($product->getStockStatus() === 'low_stock' ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                            @if($product->is_featured)
                                <div class="text-yellow-500 text-sm">⭐</div>
                            @endif
                        </div>
                        
                        <!-- Product Image -->
                        <div class="bg-gray-100 rounded-lg h-20 flex items-center justify-center text-3xl mb-3">
                            {{ $product->category->emoji ?? '🍽️' }}
                        </div>
                        
                        <!-- Product Info -->
                        <h3 class="font-semibold text-sm mb-1 line-clamp-2">{{ $product->name }}</h3>
                        <p class="text-green-600 font-bold">{{ number_format($product->price, 0) }} MT</p>
                        
                        @if($product->track_stock)
                            <p class="text-xs text-gray-500 mt-1">Stock: {{ $product->stock_quantity }}</p>
                        @endif
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <div class="text-4xl mb-4">📦</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum produto</h3>
                        <p class="text-gray-600">Não há produtos nesta categoria</p>
                    </div>
                @endforelse
            </div>
        @else
            <div class="flex items-center justify-center h-full">
                <div class="text-center">
                    <div class="text-6xl mb-4">🏪</div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Bem-vindo ao POS</h3>
                    <p class="text-gray-600">Selecione uma categoria para começar</p>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Order Panel -->
    <div class="w-96 bg-white border-l-2 border-gray-200 flex flex-col">
        <!-- Order Header -->
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-xl font-semibold">
                    {{ $currentTable ? $currentTable->name : 'Mesa não selecionada' }}
                </h2>
                @if($currentTable)
                    <span class="text-sm text-green-600 bg-green-100 px-2 py-1 rounded-full">
                        {{ $currentTable->capacity }} pessoas
                    </span>
                @endif
            </div>
            
            <div class="flex items-center justify-between">
                <p class="text-gray-600 text-sm">Pedido atual - {{ now()->format('H:i') }}</p>
                
                @if(!$currentTable)
                    <button 
                        wire:click="openTableModal"
                        class="text-sm bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Selecionar Mesa
                    </button>
                @else
                    <button 
                        wire:click="openTableModal"
                        class="text-sm text-blue-600 hover:text-blue-800"
                    >
                        Trocar Mesa
                    </button>
                @endif
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="flex-1 p-6 overflow-y-auto scrollbar-thin">
            @if(empty($cart))
                <div class="text-center py-12">
                    <div class="text-4xl mb-4 opacity-50">🛒</div>
                    <p class="text-gray-500">Carrinho vazio</p>
                    <p class="text-sm text-gray-400">Adicione produtos para começar</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($cart as $key => $item)
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <div class="flex-1">
                                <h4 class="font-medium">{{ $item['product_name'] }}</h4>
                                <p class="text-sm text-gray-500">{{ number_format($item['unit_price'], 0) }} MT cada</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button 
                                    wire:click="updateCartQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                    class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors"
                                    title="Diminuir quantidade"
                                >
                                    -
                                </button>
                                <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                                <button 
                                    wire:click="updateCartQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                    class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition-colors"
                                    title="Aumentar quantidade"
                                >
                                    +
                                </button>
                                <span class="w-20 text-right font-medium text-green-600">
                                    {{ number_format($item['total_price'], 0) }} MT
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        
        <!-- Order Footer -->
        <div class="p-6 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center text-xl font-bold mb-6">
                <span>Total:</span>
                <span class="text-green-600">{{ number_format($cartTotal, 0) }} MT</span>
            </div>
            
            <div class="space-y-3">
                <div class="flex space-x-3">
                    <button 
                        wire:click="clearCart"
                        class="flex-1 bg-gray-200 text-gray-700 py-3 px-4 rounded-lg font-medium hover:bg-gray-300 transition-colors"
                        {{ empty($cart) ? 'disabled' : '' }}
                    >
                        Limpar
                    </button>
                    <button 
                        wire:click="processPayment"
                        class="flex-1 bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ empty($cart) || !$currentTable ? 'disabled' : '' }}
                    >
                        Finalizar
                    </button>
                </div>
                
                @if(!$currentTable && !empty($cart))
                    <p class="text-center text-sm text-amber-600">
                        ⚠️ Selecione uma mesa para continuar
                    </p>
                @endif
            </div>
        </div>
    </div>



    <!-- Table Selection Modal -->
<div 
    x-data="{ show: @entangle('showTableModal') }"
    x-show="show"
    x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" 
    @click="$wire.closeTableModal()"
>
    <div 
        class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 max-h-[80vh] overflow-y-auto" 
        @click.stop
    >
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Selecionar Mesa</h2>
            <button 
                wire:click="closeTableModal"
                class="text-gray-500 hover:text-gray-700 text-2xl"
            >
                ×
            </button>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($tables as $table)
                <button
                    wire:click="selectTable({{ $table->id }})"
                    class="aspect-square flex flex-col items-center justify-center p-4 rounded-xl border-2 transition-all duration-200 hover:scale-105 {{ 
                        $table->status === 'available' ? 'bg-green-50 border-green-200 text-green-800 hover:bg-green-100' : 
                        ($table->status === 'occupied' ? 'bg-yellow-50 border-yellow-200 text-yellow-800 cursor-not-allowed' : 
                        ($table->status === 'reserved' ? 'bg-red-50 border-red-200 text-red-800 cursor-not-allowed' : 
                        'bg-gray-50 border-gray-200 text-gray-500 cursor-not-allowed')) 
                    }}"
                    {{ $table->status !== 'available' ? 'disabled' : '' }}
                >
                    <div class="text-2xl mb-2">🍽️</div>
                    <div class="font-bold">{{ $table->name }}</div>
                    <div class="text-xs">{{ $table->capacity }} lugares</div>
                    <div class="text-xs mt-1 capitalize">
                        @switch($table->status)
                            @case('available')
                                ✅ Disponível
                                @break
                            @case('occupied')
                                🔴 Ocupada
                                @break
                            @case('reserved')
                                📅 Reservada
                                @break
                            @case('maintenance')
                                🔧 Manutenção
                                @break
                        @endswitch
                    </div>
                </button>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-4xl mb-4">🏪</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhuma mesa</h3>
                    <p class="text-gray-600">Não há mesas cadastradas</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
</div>

