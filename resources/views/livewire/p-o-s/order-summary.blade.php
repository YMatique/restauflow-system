<!-- Painel do Pedido -->
<div class="flex flex-col h-full">
    @if(!$currentTable)
        <!-- Estado Inicial - Nenhuma Mesa Selecionada -->
        <div class="flex items-center justify-center h-full">
            <div class="text-center p-6">
                <svg class="w-16 h-16 mx-auto mb-4 text-zinc-400 dark:text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <p class="text-zinc-500 dark:text-zinc-400 mb-2">Selecione uma mesa</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">Clique numa mesa para começar</p>
            </div>
        </div>
    @else
        <!-- Mesa Selecionada -->
        <!-- Header -->
        <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 flex-shrink-0">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-blue-600 dark:bg-blue-500 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-lg">{{ $currentTable->name }}</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Mesa {{ $currentTable->name }}</h3>
                    <p class="text-sm font-medium
                        @if($currentTable->status === 'available') text-green-600 dark:text-green-400
                        @elseif($currentTable->status === 'occupied') text-red-600 dark:text-red-400  
                        @elseif($currentTable->status === 'reserved') text-yellow-600 dark:text-yellow-400
                        @else text-gray-600 dark:text-gray-400 @endif">
                        @switch($currentTable->status)
                            @case('available') Livre @break
                            @case('occupied') Ocupada @break  
                            @case('reserved') Reservada @break
                            @default {{ ucfirst($currentTable->status) }}
                        @endswitch
                    </p>
                </div>
            </div>
            
            <button wire:click="showProducts" 
                    class="w-full bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white py-3 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Adicionar Produtos
            </button>
        </div>

        <!-- Lista de Itens -->
        <div class="flex-1 overflow-y-auto p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-medium text-zinc-900 dark:text-zinc-100">Itens do Pedido</h4>
                <span class="text-xs text-zinc-500 dark:text-zinc-400">{{ count($cart) }} itens</span>
            </div>
            
            @if(empty($cart))
                <!-- Carrinho Vazio -->
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto mb-4 bg-zinc-100 dark:bg-zinc-800 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm">Nenhum item adicionado</p>
                    <p class="text-zinc-400 dark:text-zinc-500 text-xs mt-1">Clique em "Adicionar Produtos"</p>
                </div>
            @else
                <!-- Lista de Itens -->
                <div class="space-y-4">
                    @foreach($cart as $cartKey => $item)
                        <div class="flex items-center justify-between py-4 border-b border-zinc-200 dark:border-zinc-700 last:border-0">
                            <div class="flex-1">
                                <p class="font-medium text-sm text-zinc-900 dark:text-zinc-100">{{ $item['product_name'] }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ number_format($item['unit_price']) }} MT cada</p>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <!-- Controles de Quantidade -->
                                <div class="flex items-center gap-2">
                                    <button wire:click="updateQuantity('{{ $cartKey }}', {{ $item['quantity'] - 1 }})" 
                                            class="w-6 h-6 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 rounded text-xs transition-colors flex items-center justify-center">
                                        -
                                    </button>
                                    <span class="text-sm font-medium min-w-[20px] text-center">{{ $item['quantity'] }}</span>
                                    <button wire:click="updateQuantity('{{ $cartKey }}', {{ $item['quantity'] + 1 }})" 
                                            class="w-6 h-6 bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 dark:hover:bg-zinc-600 rounded text-xs transition-colors flex items-center justify-center">
                                        +
                                    </button>
                                </div>
                                
                                <!-- Preço Total -->
                                <span class="text-sm font-semibold text-green-600 dark:text-green-400 w-20 text-right">
                                    {{ number_format($item['total_price']) }} MT
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if(!empty($cart))
            <!-- Footer com Totais e Ações -->
            <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 flex-shrink-0">
                <!-- Totais -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Subtotal</span>
                        <span class="font-medium">{{ number_format($cartTotal) }} MT</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t border-zinc-200 dark:border-zinc-700 pt-2">
                        <span>Total</span>
                        <span class="text-green-600 dark:text-green-400">{{ number_format($cartTotal) }} MT</span>
                    </div>
                </div>
                
                <!-- Botões de Ação -->
                <div class="space-y-2">
                    <button wire:click="sendToKitchen" 
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-medium transition-colors">
                        Enviar para Cozinha
                    </button>
                    
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="finalizeOrder" 
                                class="bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                            Finalizar
                        </button>
                        <button wire:click="clearCart" 
                                class="bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                            Limpar
                        </button>
                    </div>
                </div>
            </div>
        @else
            <!-- Footer Vazio -->
            <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 flex-shrink-0">
                <div class="text-center mb-4">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-white">0 MT</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Total do pedido</div>
                </div>
            </div>
        @endif
    @endif
</div>