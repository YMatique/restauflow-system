<div>
    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ __('messages.dashboard.title')}}
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{ $breadcrumb }}
            </p>
        </div>

        <button
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-8 h-8 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Adquirir Pacote
        </button>
    </div>
    {{-- The best athlete wants his opponent at his best. --}}

     {{-- BODY --}}
    <div class="p-6 rounded-lg shadow-md space-y-4">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

                <!-- Card 3 -->
                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow text-center">
                    <div class="text-4xl mb-4"> 🧩 </div>
                    <h3 class="text-lg font-semibold mb-2">Produtos</h3>
                    <p class="text-white-600 mb-4">Controle produtos e ingredientes</p>
                    <a href="{{ route('restaurant.products') }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors">
                        🧩 Produtos
                    </a>
                </div>

                <!-- Card 1 -->
                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow text-center">
                    <div class="text-4xl mb-4">📦</div>
                    <h3 class="text-lg font-semibold mb-2">Stocks</h3>
                    <p class="text-white-600 mb-4">Acesse os Stocks</p>
                    <a href="{{ route('restaurant.stocks') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        📦 Stocks
                    </a>
                </div>

                <!-- Card 2 -->
                <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow text-center">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-lg font-semibold mb-2">Relatórios</h3>
                    <p class="text-white-600 mb-4">Visualize relatórios de vendas e performance</p>
                    <a href="{{ route('restaurant.reports') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
                        📈 Ver Relatórios
                    </a>
                </div>

            </div>
        </div>

    </div>
    {{-- END BODY --}}

</div>


