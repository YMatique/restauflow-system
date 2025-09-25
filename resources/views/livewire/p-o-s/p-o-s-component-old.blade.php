<div>
    <!-- Sidebar Esquerdo -->
    <x-slot name="sidebar">
        <div class="flex flex-col h-full">
            <!-- Header do Sidebar -->
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700 flex-shrink-0">
                <div class="flex items-center space-x-3 mb-4">
                    <div
                        class="w-12 h-12 bg-blue-600 dark:bg-blue-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
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
            </div>

            <!-- Conteúdo Scrollável -->
            <div class="flex-1 overflow-y-auto">
                <!-- Estatísticas do Dia -->
                <div class="p-6 space-y-4">
                    <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">Resumo
                        do Dia</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                            {{-- <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                                {{ $dailyStats['activeTables'] }}</div> --}}
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Mesas Ativas</div>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                            {{-- <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ $dailyStats['ordersToday'] }}</div> --}}
                            <div class="text-xs text-zinc-500 dark:text-zinc-400">Pedidos Hoje</div>
                        </div>
                    </div>

                    <div class="bg-zinc-50 dark:bg-zinc-900 p-4 rounded-lg">
                        {{-- <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ number_format($dailyStats['todayRevenue']) }} MT</div> --}}
                        <div class="text-xs text-zinc-500 dark:text-zinc-400">Faturação Hoje</div>
                    </div>
                </div>

                <!-- Acesso Rápido às Mesas -->
                <div class="p-6">
                    <h3 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider mb-4">
                        Acesso Rápido</h3>

                    <livewire:pos.quick-tables-grid />
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Área Central -->

      @if($currentView === 'tables')
        <livewire:pos.tables-grid /> 
    @elseif($currentView === 'products')
     <button wire:click="showTables" class="mb-4 bg-gray-500 text-white px-4 py-2 rounded">
            ← Voltar às Mesas
        </button>
        {{-- <livewire:pos.categories-bar /> --}}
        <livewire:pos.products-grid :currentTableId="$currentTable?->id" />
    @endif
    <!-- Painel Direito -->
    <x-slot name="rightPanel">
        
        {{-- <livewire:pos.order-summary :currentTable="$currentTable" :cart="$cart" /> --}}
        {{-- <livewire:pos.order-summary :currentTable="$currentTable" :cart="$cart"
            wire:key="order-summary-{{ $currentTable?->id ?? 'none' }}" /> --}}
            {{-- <livewire:pos.order-summary 
    :currentTable="$currentTable" 
    :cart="$cart" 
    wire:key="order-{{ $currentTable?->id ?? 'none' }}" /> --}}
     <livewire:pos.order-summary 
        :currentTable="$currentTable" 
        wire:key="order-summary-main" 
    />
        {{-- <livewire:pos.order-summary :currentTable="$currentTable" :cart="$cart" :cartTotal="$cartTotal" :cartCount="$cartCount" /> --}}
    </x-slot>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-24 right-6 z-50 space-y-2">
        <!-- Toasts serão inseridos aqui via JavaScript -->
    </div>

    @push('scripts')
        <script>
            // Listener para toasts
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('toast', (data) => {
                    showToast(data[0]);
                });
            });

            function showToast(data) {
                const container = document.getElementById('toast-container');
                const toast = document.createElement('div');

                const colors = {
                    success: 'bg-green-600',
                    error: 'bg-red-600',
                    warning: 'bg-yellow-600',
                    info: 'bg-blue-600'
                };

                const icons = {
                    success: '✓',
                    error: '✕',
                    warning: '⚠',
                    info: 'ℹ'
                };

                toast.className =
                    `${colors[data.type]} text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 flex items-center gap-3`;
                toast.innerHTML = `
            <span class="text-lg">${icons[data.type]}</span>
            <span>${data.message}</span>
            <button onclick="this.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;

                container.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.classList.remove('translate-x-full');
                }, 100);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    toast.classList.add('translate-x-full');
                    setTimeout(() => {
                        if (toast.parentElement) {
                            toast.remove();
                        }
                    }, 300);
                }, 5000);
            }
        </script>
    @endpush
</div>
