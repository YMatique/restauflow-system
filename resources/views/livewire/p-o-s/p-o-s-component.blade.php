{{-- resources/views/livewire/p-o-s/p-o-s-component.blade.php --}}
<div class="flex overflow-hidden h-full">

    <!-- Sidebar Esquerdo -->
    {{-- <x-slot name="sidebar"> --}}
        <div class="w-80 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl mr-4 flex flex-col overflow-hidden">
        <div class="flex flex-col h-full pt-12">
            <!-- Header do Sidebar -->
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 flex-shrink-0">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-zinc-900 dark:text-white">RestauFlow</h1>
                        <span class="text-xs text-green-600 font-semibold">POS Ativo</span>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="p-6 space-y-4">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase">Resumo</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ count($tables) }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Mesas</div>
                    </div>
                    <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ count($cart) }}</div>
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Itens</div>
                    </div>
                </div>

                <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format(collect($cart)->sum('total_price')) }} MT</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Total Carrinho</div>
                </div>
            </div>

            <!-- Mesas Rápidas -->
            <div class="p-6">
                <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase mb-4">Acesso Rápido</h3>
                <div class="grid grid-cols-4 gap-2">
                    @foreach(collect($tables)->take(8) as $table)
                        <button wire:click="selectTable({{ $table->id }})"
                                class="w-12 h-12 rounded-lg text-sm font-bold transition-all text-white
                                @if($table->status === 'available') bg-green-500 hover:bg-green-600
                                @elseif($table->status === 'occupied') bg-red-500 hover:bg-red-600
                                @elseif($table->status === 'reserved') bg-yellow-500 hover:bg-yellow-600
                                @else bg-gray-500 hover:bg-gray-600 @endif">
                            {{ $table->name }}
                        </button>
                    @endforeach
                    
                    @for($i = count(collect($tables)->take(8)); $i < 8; $i++)
                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">-</div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
    {{-- </x-slot> --}}

    <!-- Área Central -->
    <div class="flex-1 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl mr-4 flex flex-col overflow-hidden">
          
    <div class="pt-12">
        @if($currentView === 'tables')
            <!-- VIEW DE MESAS -->
            <div class="p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-2">Selecionar Mesa</h2>
                    <p class="text-zinc-600 dark:text-zinc-400">Clique numa mesa para começar um novo pedido</p>
                </div>

                <!-- Grid de Mesas -->
                <div class="grid grid-cols-6 xl:grid-cols-6 lg:grid-cols-5 md:grid-cols-4 sm:grid-cols-3 gap-4">
                    @forelse($tables as $table)
                        <div wire:click="selectTable({{ $table->id }})"
                             class="w-20 h-20 rounded-xl flex items-center justify-center font-bold cursor-pointer transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl text-white
                             @if($table->status === 'available') bg-green-500 hover:bg-green-600
                             @elseif($table->status === 'occupied') bg-red-500 hover:bg-red-600
                             @elseif($table->status === 'reserved') bg-yellow-500 hover:bg-yellow-600
                             @else bg-gray-500 hover:bg-gray-600 @endif">
                            <div class="text-center">
                                <div class="text-lg font-bold">{{ $table->name }}</div>
                                @if($table->capacity)
                                    <div class="text-xs opacity-80">{{ $table->capacity }}p</div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">Nenhuma mesa configurada</h3>
                            <p class="text-gray-500">Configure as mesas no sistema de gestão</p>
                        </div>
                    @endforelse
                </div>

                <!-- Legenda -->
                <div class="mt-8 flex items-center justify-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-500 rounded"></div>
                        <span>Disponível</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-red-500 rounded"></div>
                        <span>Ocupada</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                        <span>Reservada</span>
                    </div>
                </div>
            </div>
        @else
            <!-- VIEW DE PRODUTOS -->
            <div class="p-6">
                <!-- Header com botão voltar -->
                <div class="mb-6 flex items-center gap-4">
                    <button wire:click="backToTables" 
                            class="flex items-center gap-2 px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded-lg font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Voltar às Mesas
                    </button>
                    
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-zinc-900 dark:text-white mb-2">
                            Produtos - Mesa {{ $currentTable?->name }}
                        </h2>
                        <p class="text-zinc-600 dark:text-zinc-400">Clique nos produtos para adicionar ao carrinho</p>
                    </div>
                </div>

                <!-- Barra de Categorias -->
                <div class="flex gap-2 mb-6 overflow-x-auto pb-2">
                    <button wire:click="selectCategory(null)"
                            class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors
                            @if(is_null($selectedCategory)) bg-blue-600 text-white
                            @else bg-zinc-200 text-zinc-700 hover:bg-zinc-300 @endif">
                        Todas
                    </button>

                    @foreach($categories as $category)
                        <button wire:click="selectCategory({{ $category->id }})"
                                class="px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors
                                @if($selectedCategory == $category->id) bg-blue-600 text-white
                                @else bg-zinc-200 text-zinc-700 hover:bg-zinc-300 @endif">
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>

                <!-- Grid de Produtos -->
                <div class="grid grid-cols-4 xl:grid-cols-4 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-2 gap-4">
                    @forelse($products as $product)
                        <div wire:click="addToCart({{ $product->id }})"
                             class="bg-zinc-50 rounded-xl border border-zinc-200 p-4 cursor-pointer hover:border-blue-400 hover:shadow-lg transition-all transform hover:scale-105">
                            
                            <!-- Imagem Placeholder -->
                            <div class="w-full h-24 bg-zinc-200 rounded-lg mb-3 flex items-center justify-center">
                                <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2-2z"></path>
                                </svg>
                            </div>
                            
                            <!-- Info do Produto -->
                            <div>
                                <h3 class="font-medium text-sm text-zinc-900 mb-1">{{ $product->name }}</h3>
                                <div class="flex items-center justify-between">
                                    <p class="text-green-600 font-bold text-sm">{{ number_format($product->price) }} MT</p>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded">Disponível</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <h3 class="text-lg font-semibold text-gray-600 mb-2">Nenhum produto disponível</h3>
                            <p class="text-gray-500">Configure produtos no sistema de gestão</p>
                        </div>
                    @endforelse
                </div>

                @if(count($products) > 0)
                    <div class="mt-6 text-center text-sm text-zinc-500">
                        {{ count($products) }} produto(s) disponível(is)
                    </div>
                @endif
            </div>
        @endif
    </div>
    </div>

    <!-- Painel Direito (Order Summary) -->
    {{-- <x-slot name="rightPanel"> --}}
        <div class="w-80 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl flex flex-col overflow-hidden">
            
        <div class="flex flex-col h-full pt-12">
           
            @if(!$currentTable || $currentView === 'tables')
                <!-- Estado Inicial -->
                <div class="flex items-center justify-center h-full">
                    <div class="text-center p-6">
                        <svg class="w-16 h-16 mx-auto mb-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <p class="text-zinc-500 mb-2">Selecione uma mesa</p>
                        <p class="text-xs text-zinc-400">Clique numa mesa para começar</p>
                    </div>
                </div>
            @else
                <!-- Mesa Selecionada -->
                
                <!-- Header -->
                <div class="p-6 border-b border-zinc-200 bg-zinc-50 flex-shrink-0">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ $currentTable->name }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-zinc-900">Mesa {{ $currentTable->name }}</h3>
                            <p class="text-sm font-medium text-green-600">Ativa</p>
                        </div>
                    </div>
                </div>

                <!-- Lista de Itens -->
                <div class="flex-1 overflow-y-auto p-6">
                    @if(empty($cart))
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-zinc-200 rounded-full flex items-center justify-center text-xl mx-auto mb-3">🛒</div>
                            <p class="text-zinc-500 mb-2">Carrinho vazio</p>
                            <p class="text-xs text-zinc-400">Clique nos produtos para adicionar</p>
                        </div>
                    @else
                        <!-- Items do Carrinho -->
                        <div class="space-y-3">
                            @foreach($cart as $cartKey => $item)
                                <div class="bg-white rounded-lg border p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-sm text-zinc-900">{{ $item['product_name'] }}</h4>
                                            <p class="text-xs text-zinc-500">{{ number_format($item['unit_price']) }} MT cada</p>
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
                                                    class="w-8 h-8 rounded-full bg-zinc-200 flex items-center justify-center text-zinc-600 hover:bg-zinc-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <span class="w-12 text-center font-medium">{{ $item['quantity'] }}</span>
                                            <button wire:click="updateQuantity({{ $cartKey }}, {{ $item['quantity'] + 1 }})"
                                                    class="w-8 h-8 rounded-full bg-zinc-200 flex items-center justify-center text-zinc-600 hover:bg-zinc-300">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="font-bold text-green-600">{{ number_format($item['total_price']) }} MT</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Footer/Actions -->
                <div class="p-6 border-t border-zinc-200 bg-zinc-50 flex-shrink-0">
                    @if(!empty($cart))
                        <!-- Total -->
                        <div class="mb-4 p-4 bg-white rounded-lg">
                            <div class="flex items-center justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span class="text-green-600">{{ number_format(collect($cart)->sum('total_price')) }} MT</span>
                            </div>
                            <div class="text-xs text-zinc-500 mt-1">{{ collect($cart)->sum('quantity') }} item(s)</div>
                        </div>

                        <!-- Botões -->
                        <div class="space-y-3">
                            <button wire:click="finalizeOrder"
                                    class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors">
                                Finalizar Pedido
                            </button>
                            <div class="grid grid-cols-2 gap-3">
                                <button wire:click="clearCart" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg text-sm">Limpar</button>
                                <button wire:click="backToTables" class="bg-zinc-500 hover:bg-zinc-600 text-white py-2 px-4 rounded-lg text-sm">Voltar</button>
                            </div>
                        </div>
                    @else
                        <div class="text-center space-y-3">
                            <p class="text-zinc-500 text-sm">Clique nos produtos para adicionar</p>
                            <button wire:click="backToTables" class="w-full bg-zinc-500 hover:bg-zinc-600 text-white py-3 px-4 rounded-lg">
                                ← Voltar às Mesas
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
        </div>
    {{-- </x-slot> --}}

    <!-- Toast Container -->
    <div id="toast-container" class="fixed top-24 right-6 z-40 space-y-2"></div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('toast', (data) => {
                    const container = document.getElementById('toast-container');
                    const toast = document.createElement('div');
                    const colors = {
                        success: 'bg-green-600',
                        error: 'bg-red-600',
                        warning: 'bg-yellow-600',
                        info: 'bg-blue-600'
                    };
                    toast.className = `${colors[data[0].type]} text-white px-4 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 flex items-center gap-2`;
                    toast.innerHTML = `<span>${data[0].message}</span>`;
                    container.appendChild(toast);
                    setTimeout(() => toast.classList.remove('translate-x-full'), 100);
                    setTimeout(() => {
                        toast.classList.add('translate-x-full');
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                });
            });
        </script>
    @endpush
</div>