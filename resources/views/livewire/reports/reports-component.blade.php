{{-- resources/views/livewire/reports/reports-component.blade.php --}}
<div>
    <x-layouts.header 
        title="Relatórios de Performance" 
        breadcrumb="Dashboard > Relatórios"
        variant="success"
    >
        <x-slot:actions>
            <div class="flex items-center space-x-3">
                <select 
                    wire:model.live="period"
                    class="bg-white/20 border border-white/30 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-white/50"
                >
                    <option value="today">Hoje</option>
                    <option value="week">7 dias</option>
                    <option value="month">30 dias</option>
                    <option value="quarter">3 meses</option>
                    <option value="year">Ano</option>
                    <option value="custom">Personalizado</option>
                </select>
                
                @if($period === 'custom')
                    <input 
                        wire:model="startDate"
                        type="date"
                        class="bg-white/20 border border-white/30 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-white/50"
                    >
                    <input 
                        wire:model="endDate"
                        type="date"
                        class="bg-white/20 border border-white/30 text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-white/50"
                    >
                    <x-ui.button 
                        wire:click="applyCustomPeriod"
                        variant="secondary"
                        size="sm"
                    >
                        Aplicar
                    </x-ui.button>
                @endif
            </div>
        </x-slot:actions>
    </x-layouts.header>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-ui.stats-card 
                title="Receita Total"
                value="{{ number_format($stats['total_revenue'], 0) }} MT"
                subtitle="No período selecionado"
                icon="💰"
                color="green"
                trend="up"
            >
                +15.3% vs período anterior
            </x-ui.stats-card>
            
            <x-ui.stats-card 
                title="Pedidos Realizados"
                value="{{ $stats['total_orders'] }}"
                subtitle="Total de transações"
                icon="📋"
                color="blue"
                trend="up"
            >
                +8.7% vs período anterior
            </x-ui.stats-card>
            
            <x-ui.stats-card 
                title="Clientes Únicos"
                value="{{ $stats['unique_customers'] }}"
                subtitle="Clientes atendidos"
                icon="👥"
                color="yellow"
                trend="down"
            >
                -2.1% vs período anterior
            </x-ui.stats-card>
            
            <x-ui.stats-card 
                title="Ticket Médio"
                value="{{ number_format($stats['average_ticket'], 0) }} MT"
                subtitle="Valor médio por pedido"
                icon="🎯"
                color="purple"
                trend="up"
            >
                +12.4% vs período anterior
            </x-ui.stats-card>
        </div>
        
        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Chart -->
            <x-ui.card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Receita por Dia</h3>
                    <div class="flex space-x-2">
                        <button class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full">Receita</button>
                        <button class="px-3 py-1 text-xs text-gray-500 hover:bg-gray-100 rounded-full">Pedidos</button>
                    </div>
                </div>
                
                <div class="h-64 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                    <div class="text-center text-gray-500">
                        <div class="text-3xl mb-2">📊</div>
                        <p>Gráfico de receita diária</p>
                        <p class="text-sm">{{ count($dailyRevenue) }} pontos de dados</p>
                    </div>
                </div>
            </x-ui.card>
            
            <!-- Categories Chart -->
            <x-ui.card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Vendas por Categoria</h3>
                </div>
                
                <div class="h-64 bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-300">
                    <div class="text-center text-gray-500">
                        <div class="text-3xl mb-2">🥧</div>
                        <p>Gráfico de pizza</p>
                        <p class="text-sm">Distribuição por categorias</p>
                    </div>
                </div>
            </x-ui.card>
        </div>
        
        <!-- Tables Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Products -->
            <x-ui.card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Top Produtos</h3>
                    <a href="#" class="text-sm text-green-600 hover:text-green-700 font-medium">Ver todos</a>
                </div>
                
                <div class="space-y-4">
                    @forelse($topProducts->take(5) as $index => $product)
                        <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-100 text-gray-800' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }}">
                                {{ $index + 1 }}
                            </div>
                            
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $product->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $product->sale_items_count ?? 0 }} vendas</p>
                            </div>
                            
                            <div class="text-right">
                                <div class="font-bold text-green-600">{{ number_format($product->sale_items_sum_total_price ?? 0, 0) }} MT</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="text-3xl mb-2">📦</div>
                            <p class="text-gray-500">Nenhuma venda no período</p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>
            
            <!-- Top Customers -->
            <x-ui.card>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Melhores Clientes</h3>
                    <a href="#" class="text-sm text-green-600 hover:text-green-700 font-medium">Ver todos</a>
                </div>
                
                <div class="space-y-4">
                    @forelse($topCustomers->take(5) as $index => $customer)
                        <div class="flex items-center space-x-4 p-3 hover:bg-gray-50 rounded-lg">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' : ($index === 1 ? 'bg-gray-100 text-gray-800' : ($index === 2 ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800')) }}">
                                {{ $index + 1 }}
                            </div>
                            
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $customer->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $customer->sales_count ?? 0 }} pedidos</p>
                            </div>
                            
                            <div class="text-right">
                                <div class="font-bold text-green-600">{{ number_format($customer->sales_sum_total ?? 0, 0) }} MT</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="text-3xl mb-2">👥</div>
                            <p class="text-gray-500">Nenhum cliente no período</p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>
        </div>
    </div>
</div>