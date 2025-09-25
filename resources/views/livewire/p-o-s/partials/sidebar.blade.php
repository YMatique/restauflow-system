{{-- resources/views/livewire/pos/partials/sidebar.blade.php --}}
<div class="flex flex-col h-full">
    <!-- Header do Sidebar -->
    <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 flex-shrink-0">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-12 h-12 bg-blue-600 dark:bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-zinc-900 dark:text-white leading-none">RestauFlow</h1>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">POS</span>
                    <span class="text-xs text-zinc-400 dark:text-zinc-500">•</span>
                    <span class="text-xs font-semibold text-green-600 dark:text-green-400">Ativo</span>
                </div>
            </div>
        </div>

        {{-- Botão Voltar (quando uma mesa está selecionada) --}}
        @if($currentTable && $currentView === 'products')
            <div class="mb-4">
                <button wire:click="backToTables" 
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-lg font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Voltar às Mesas
                </button>
            </div>
        @endif
    </div>

    <!-- Conteúdo Scrollável -->
    <div class="flex-1 overflow-y-auto">
        <!-- Estatísticas do Dia -->
        <div class="p-6 space-y-4">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Resumo do Dia</h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-zinc-900 dark:text-white">{{ count($tables) }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Mesas Totais</div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $cartCount }}</div>
                    <div class="text-xs text-zinc-500 dark:text-zinc-400">Itens no Carrinho</div>
                </div>
            </div>

            <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($cartTotal) }} MT</div>
                <div class="text-xs text-zinc-500 dark:text-zinc-400">Total do Carrinho</div>
            </div>
        </div>

        <!-- Acesso Rápido às Mesas -->
        <div class="p-6">
            <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-4">
                Acesso Rápido</h3>

            <div class="grid grid-cols-4 gap-2">
                @foreach($quickTables as $table)
                    <button wire:click="selectTable({{ $table->id }})"
                            @class([
                                'w-12 h-12 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-105 text-white',
                                'bg-green-500 hover:bg-green-600' => $table->status === 'available',
                                'bg-red-500 hover:bg-red-600' => $table->status === 'occupied',
                                'bg-yellow-500 hover:bg-yellow-600' => $table->status === 'reserved',
                                'bg-gray-500 hover:bg-gray-600' => $table->status === 'maintenance'
                            ])>
                        {{ $table->name }}
                    </button>
                @endforeach
                
                @for($i = count($quickTables); $i < 8; $i++)
                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">
                        -
                    </div>
                @endfor
            </div>
        </div>
    </div>
</div>