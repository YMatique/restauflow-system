{{-- resources/views/livewire/pos/pos-component.blade.php --}}
<div class="flex h-full">
    <!-- Categories Sidebar -->
    <div class="w-48 bg-white border-r-2 border-gray-200 p-4 overflow-y-auto">
        <div class="space-y-2">
            @foreach($categories as $category)
                <button 
                    wire:click="selectCategory({{ $category->id }})"
                    class="w-full flex flex-col items-center p-3 rounded-xl transition-all duration-200 {{ $selectedCategory === $category->id ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}"
                >
                    <div class="text-2xl mb-2">{{ $category->emoji }}</div>
                    <div class="text-xs font-medium text-center">{{ $category->name }}</div>
                </button>
            @endforeach
        </div>
    </div>
    
    <!-- Products Grid -->
    <div class="flex-1 p-6 bg-gray-50 overflow-y-auto">
        <div class="grid grid-cols-3 gap-4">
            @foreach($products as $product)
                <div 
                    wire:click="addToCart({{ $product->id }})"
                    class="bg-white rounded-xl p-4 cursor-pointer transition-all duration-200 hover:shadow-lg hover:-translate-y-1 {{ !$product->canSell() ? 'opacity-50 cursor-not-allowed' : '' }}"
                >
                    <!-- Stock Indicator -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-3 h-3 rounded-full {{ $product->getStockStatus() === 'in_stock' ? 'bg-green-500' : ($product->getStockStatus() === 'low_stock' ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                    </div>
                    
                    <!-- Product Image -->
                    <div class="bg-gray-100 rounded-lg h-20 flex items-center justify-center text-3xl mb-3">
                        {{ $product->category->emoji ?? '🍽️' }}
                    </div>
                    
                    <!-- Product Info -->
                    <h3 class="font-semibold text-sm mb-1">{{ $product->name }}</h3>
                    <p class="text-green-600 font-bold">{{ number_format($product->price, 0) }} MT</p>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Order Panel -->
    <div class="w-96 bg-white border-l-2 border-gray-200 flex flex-col">
        <!-- Order Header -->
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold">
                {{ $currentTable ? $currentTable->name : 'Mesa não selecionada' }}
            </h2>
            <p class="text-gray-600 text-sm">Pedido atual - {{ now()->format('H:i') }}</p>
        </div>
        
        <!-- Order Items -->
        <div class="flex-1 p-6 overflow-y-auto">
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
                            </div>
                            <div class="flex items-center space-x-3">
                                <button 
                                    wire:click="updateCartQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                    class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200"
                                >
                                    -
                                </button>
                                <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                                <button 
                                    wire:click="updateCartQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                    class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center hover:bg-gray-200"
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
        <div class="p-6 border-t border-gray-200">
            <div class="flex justify-between items-center text-xl font-bold mb-6">
                <span>Total:</span>
                <span>{{ number_format($cartTotal, 0) }} MT</span>
            </div>
            
            <div class="flex space-x-3">
                <x-ui.button 
                    wire:click="clearCart"
                    variant="secondary" 
                    size="lg"
                    class="flex-1"
                >
                    Limpar
                </x-ui.button>
                <x-ui.button 
                    wire:click="processPayment"
                    variant="success" 
                    size="lg"
                    class="flex-1"
                    :disabled="empty($cart)"
                >
                    Finalizar
                </x-ui.button>
            </div>
        </div>
    </div>
</div>