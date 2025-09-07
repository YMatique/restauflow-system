{{-- resources/views/livewire/dashboard/dashboard-component.blade.php --}}
<div>
    {{-- <x-layouts.components.header
        title="Dashboard Principal"
        breadcrumb="Dashboard"
        variant="primary"
    /> --}}
    @include('layouts.components.header', [
        'title' => 'Página Principal',
        'subtitle' => 'Bem-vindo ao sistema',
        'variant' => 'purple',
        'breadcrumb' => 'Home',

    ])



    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-ui.stats-card title="Vendas Hoje" value="R$ 12.450" subtitle="23 pedidos" icon="💰" color="green"
                trend="up">
                +15.3% vs ontem
            </x-ui.stats-card>

            <x-ui.stats-card title="Mesas Ocupadas" value="8/12" subtitle="66% ocupação" icon="🍽️" color="blue"
                trend="neutral">
                Normal para o horário
            </x-ui.stats-card>

            <x-ui.stats-card title="Produtos Baixos" value="3" subtitle="Requer atenção" icon="📦"
                color="yellow" trend="down">
                -2 desde ontem
            </x-ui.stats-card>

            <x-ui.stats-card title="Turno Ativo" value="6h 32min" subtitle="João Silva" icon="⏰" color="purple" />
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <x-ui.card>
                <div class="text-center">
                    <div class="text-4xl mb-4">🛒</div>
                    <h3 class="text-lg font-semibold mb-2">Ponto de Venda</h3>
                    <p class="text-gray-600 mb-4">Acesse o sistema POS para realizar vendas</p>
                    <x-ui.button href="{{ route('restaurant.pos') }}" variant="primary" icon="🚀" :isLink="true">
                        Abrir POS
                    </x-ui.button>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="text-center">
                    <div class="text-4xl mb-4">📊</div>
                    <h3 class="text-lg font-semibold mb-2">Relatórios</h3>
                    <p class="text-gray-600 mb-4">Visualize relatórios de vendas e performance</p>
                    <x-ui.button href="{{ route('restaurant.reports') }}" variant="secondary" icon="📈">
                        Ver Relatórios
                    </x-ui.button>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="text-center">
                    <div class="text-4xl mb-4">📦</div>
                    <h3 class="text-lg font-semibold mb-2">Gestão de Stock</h3>
                    <p class="text-gray-600 mb-4">Controle produtos e ingredientes</p>
                    <x-ui.button href="{{ route('restaurant.stocks') }}" variant="warning" icon="📋" :isLink="true">
                        Gerir Stock
                    </x-ui.button>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
