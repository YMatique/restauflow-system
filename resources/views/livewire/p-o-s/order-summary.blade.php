<div class="h-full flex flex-col">
    <!-- Header do Carrinho -->
    <div class="p-4 border-b bg-gray-50">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-lg font-semibold">Pedido Atual</h3>
            @if(!empty($cart))
                <button 
                    wire:click="clearCart"
                    class="text-sm text-red-600 hover:text-red-700"
                >
                    Limpar
                </button>
            @endif
        </div>
        
        <div class="text-sm text-gray-600">
            {{ $cartCount }} {{ $cartCount === 1 ? 'item' : 'itens' }}
        </div>
    </div>

    <!-- Lista de Items -->
    <div class="flex-1 overflow-y-auto p-4">
        @forelse($cart as $key => $item)
            <div class="mb-3 p-3 bg-white rounded-lg border">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h4 class="font-medium text-sm">{{ $item['product_name'] }}</h4>
                        <p class="text-xs text-gray-500">
                            {{ number_format($item['unit_price'], 2) }} MT
                        </p>
                    </div>
                    <button 
                        wire:click="removeItem('{{ $key }}')"
                        class="text-red-500 hover:text-red-700"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button 
                            wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                            class="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center"
                            @if($item['quantity'] <= 1) disabled @endif
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        
                        <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                        
                        <button 
                            wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                            class="w-7 h-7 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>

                    <div class="font-semibold">
                        {{ number_format($item['total_price'], 2) }} MT
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 text-gray-400">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p class="text-sm">Carrinho vazio</p>
                <p class="text-xs mt-1">Adicione produtos para começar</p>
            </div>
        @endforelse
    </div>

    <!-- Footer com Total e Botão de Pagamento -->
    <div class="border-t p-4 bg-gray-50">
        <div class="mb-4">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-medium">{{ number_format($cartTotal, 2) }} MT</span>
            </div>
            <div class="flex justify-between text-lg font-bold">
                <span>Total</span>
                <span>{{ number_format($cartTotal, 2) }} MT</span>
            </div>
        </div>

        <button 
            wire:click="openPayment"
            @disabled(empty($cart))
            @class([
                'w-full py-3 rounded-lg font-semibold transition-colors',
                'bg-green-600 text-white hover:bg-green-700' => !empty($cart),
                'bg-gray-300 text-gray-500 cursor-not-allowed' => empty($cart)
            ])
        >
            @if(empty($cart))
                Adicione itens
            @else
                Finalizar Pedido
            @endif
        </button>
    </div>
</div>