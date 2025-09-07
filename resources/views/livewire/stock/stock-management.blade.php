<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ $breadcrumb }}
            </p>
        </div>

        <button
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Botão (opcional)
        </button>
    </div>
    {{-- The best athlete wants his opponent at his best. --}}

     {{-- BODY --}}
    <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md space-y-4">
            {{-- <button wire:click="testFunction()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors">
                Teste de toast
            </button> --}}

            {{-- Aqui você pode adicionar mais componentes --}}
            {{-- <p class="text-gray-700 dark:text-gray-200">
                Corpo do conteúdo vai aqui. Você pode adicionar cards, tabelas ou qualquer outro elemento.
            </p> --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            {{-- <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <!-- Estatística 1 -->
                <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg shadow">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-2xl">💰</div>
                        <div class="text-right">
                            <p class="text-gray-500 text-sm">Vendas Hoje</p>
                            <p class="text-xl font-bold">Produtos</p>
                        </div>
                    </div>
                    <p class="text-green-600 text-sm">+15.3% vs ontem</p>
                    <p class="text-gray-500 text-xs">23 pedidos</p>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg shadow">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-2xl">📦</div> <!-- ícone de produto -->
                        <div class="text-right">
                            <p class="text-gray-500 text-sm">Produtos Ativos</p>
                            <p class="text-xl font-bold">120</p> <!-- quantidade de produtos -->
                        </div>
                    </div>
                    <p class="text-blue-600 text-sm">Estoque baixo: 5</p>
                    <p class="text-gray-500 text-xs">Categorias: 8</p>
                </div>


                <!-- Estatística 2 -->
                <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg shadow">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-2xl">🍽️</div>
                        <div class="text-right">
                            <p class="text-gray-500 text-sm">Mesas Ocupadas</p>
                            <p class="text-xl font-bold">8/12</p>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm">Normal para o horário</p>
                    <p class="text-gray-500 text-xs">66% ocupação</p>
                </div>

                <!-- Estatística 3 -->
                <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg shadow">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-2xl">📦</div>
                        <div class="text-right">
                            <p class="text-gray-500 text-sm">Produtos Baixos</p>
                            <p class="text-xl font-bold">3</p>
                        </div>
                    </div>
                    <p class="text-red-600 text-sm">-2 desde ontem</p>
                    <p class="text-gray-500 text-xs">Requer atenção</p>
                </div>

                <!-- Estatística 4 -->
                <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg shadow">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-2xl">⏰</div>
                        <div class="text-right">
                            <p class="text-gray-500 text-sm">Turno Ativo</p>
                            <p class="text-xl font-bold">6h 32min</p>
                        </div>
                    </div>
                    <p class="text-gray-500 text-xs">João Silva</p>
                </div>

            </div> --}}

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

                <!-- Card 3 -->
                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow text-center">
                    <div class="text-4xl mb-4"> 🧩 </div>
                    <h3 class="text-lg font-semibold mb-2">Produtos</h3>
                    <p class="text-gray-600 mb-4">Controle produtos e ingredientes</p>
                    <a href="{{ route('restaurant.products') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors">
                        🧩 Produtos
                    </a>
                </div>

                <!-- Card 1 -->
                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow text-center">
                    <div class="text-4xl mb-4">🛒</div>
                    <h3 class="text-lg font-semibold mb-2">Ponto de Venda</h3>
                    <p class="text-gray-600 mb-4">Acesse o sistema POS para realizar vendas</p>
                    <a href="{{ route('restaurant.pos') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        🚀 Abrir POS
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow text-center">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-lg font-semibold mb-2">Relatórios</h3>
                    <p class="text-gray-600 mb-4">Visualize relatórios de vendas e performance</p>
                    <a href="{{ route('restaurant.reports') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                        📈 Ver Relatórios
                    </a>
                </div>


            </div>
        </div>

    </div>
    {{-- END BODY --}}

</div>


