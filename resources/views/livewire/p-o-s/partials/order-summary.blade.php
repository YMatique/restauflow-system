{{-- resources/views/livewire/pos/partials/order-summary.blade.php --}}
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
                            @default {{ ucfirst($currentTable->status) }} @break
                        @endswitch
                    </p>
                </div>
            </div>
        </div>

        <!-- Lista de Itens -->
        <div class="flex-1 overflow-y-auto p-6">
            @if(empty($cart))
                <div class="text-center py-8">
                    <div class="w-12 h-12 bg-zinc-200 dark:bg-zinc-700 rounded-full flex items-center justify-center text-xl mx-auto mb-3">
                        🛒
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 mb-2">Nenhum item adicionado</p>
                    <p class="text-xs text-zinc-400 dark:text-zinc-500">Clique em "Adicionar Produtos"</p>
                </div>
            @else
                <!-- Itens do Carrinho -->
                <div class="space-y-3">
                    @foreach($cart as $cartKey => $item)
                        <div class="bg-white dark:bg-zinc-800 rounded-lg border border-zinc-200 dark:border-zinc-700 p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <h4 class="font-medium text-sm text-zinc-900 dark:text-zinc-100">
                                        {{ $item['product_name'] }}
                                    </h4>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                        {{ number_format($item['unit_price']) }} MT cada
                                    </p>
                                </div>
                                <button wire:click="removeFromCart({{ $cartKey }})" 
                                        class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Controles de Quantidade -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button wire:click="updateQuantity({{ $cartKey }}, {{ $item['quantity'] - 1 }})"
                                            class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-600 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <span class="w-12 text-center font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <button wire:click="updateQuantity({{ $cartKey }}, {{ $item['quantity'] + 1 }})"
                                            class="w-8 h-8 rounded-full bg-zinc-200 dark:bg-zinc-600 flex items-center justify-center text-zinc-600 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div class="font-bold text-green-600 dark:text-green-400">
                                    {{ number_format($item['total_price']) }} MT
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Footer/Actions -->
        <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 flex-shrink-0">
            @if(!empty($cart))
                <!-- Total -->
                <div class="mb-4 p-4 bg-white dark:bg-zinc-800 rounded-lg">
                    <div class="flex items-center justify-between text-lg font-bold">
                        <span class="text-zinc-900 dark:text-zinc-100">Total:</span>
                        <span class="text-green-600 dark:text-green-400">{{ number_format($cartTotal) }} MT</span>
                    </div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        {{ $cartCount }} item(s)
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="space-y-3">
                    <button wire:click="finalizeOrder"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Finalizar Pedido
                    </button>

                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="clearCart"
                                class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                            Limpar
                        </button>
                        <button wire:click="backToTables"
                                class="bg-zinc-500 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                            Cancelar
                        </button>
                    </div>
                </div>
            @else
                <!-- Botão para Adicionar Produtos (quando carrinho vazio) -->
                @if($currentView === 'tables')
                    <button disabled
                            class="w-full bg-zinc-300 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400 font-medium py-3 px-4 rounded-lg cursor-not-allowed">
                        Selecione uma mesa primeiro
                    </button>
                @else
                    <div class="text-center">
                        <p class="text-zinc-500 dark:text-zinc-400 text-sm mb-3">
                            Clique nos produtos para adicionar ao carrinho
                        </p>
                        <button wire:click="backToTables"
                                class="bg-zinc-500 hover:bg-zinc-600 text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                            Voltar às Mesas
                        </button>
                    </div>
                @endif
            @endif
        </div>
    @endif
</div>