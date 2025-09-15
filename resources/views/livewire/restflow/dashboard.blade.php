<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white dark:bg-zinc-800 p-6 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700">
        <div class="space-y-1">
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                {{ __('messages.dashboard.title') }}
            </h1>
            <nav class="text-sm text-zinc-500 dark:text-zinc-400">
                @foreach ($breadcrumb as $item)
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="text-zinc-700 dark:text-zinc-300">{{ $item['label'] }}</span>
                    @endif
                    @if(!$loop->last)
                        <span class="mx-2 text-zinc-400">/</span>
                    @endif
                @endforeach
            </nav>
        </div>

        <button wire:click="createProduct"
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
           <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
           </svg>
           Adquirir Pacote
       </button>

    </div>
     <!-- Main Content Card -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">

        <div class="overflow-x-auto">

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

    </div>

</div>