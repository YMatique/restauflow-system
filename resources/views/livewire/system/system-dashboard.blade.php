<div class="space-y-6">
    <!-- Header com controles -->
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-xl shadow-lg p-6 text-white">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-4 lg:space-y-0">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Dashboard do Sistema</h1>
                <p class="mt-2 text-indigo-100">Visão geral das operações do sistema</p>
            </div>
            
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <!-- Filtro de período -->
                <select wire:model.live="selectedPeriod" 
                        class="px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/30">
                    <option value="7">Últimos 7 dias</option>
                    <option value="30">Últimos 30 dias</option>
                    <option value="90">Últimos 90 dias</option>
                    <option value="365">Último ano</option>
                </select>

                <!-- Toggle tempo real -->
                <button wire:click="toggleRealtime"
                        class="inline-flex items-center px-4 py-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-lg text-white hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/30 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    @if($realtimeEnabled)
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-1"></span>
                            Tempo Real
                        </span>
                    @else
                        Ativar Tempo Real
                    @endif
                </button>

                <!-- Export -->
                <button wire:click="exportData" 
                        class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg font-semibold hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-white/30 transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Exportar
                </button>
            </div>
        </div>
    </div>

    <!-- Alertas do Sistema -->
    @if(count($systemAlerts) > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($systemAlerts as $alert)
                <div class="
                    @if($alert['type'] === 'error') bg-red-50 border-l-4 border-red-400 dark:bg-red-900/20 dark:border-red-400
                    @elseif($alert['type'] === 'warning') bg-yellow-50 border-l-4 border-yellow-400 dark:bg-yellow-900/20 dark:border-yellow-400
                    @else bg-green-50 border-l-4 border-green-400 dark:bg-green-900/20 dark:border-green-400
                    @endif
                    p-4 rounded-lg shadow-sm">
                    <div class="flex justify-between items-start">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                @if($alert['type'] === 'error')
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                    </svg>
                                @elseif($alert['type'] === 'warning')
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                @else
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold
                                    @if($alert['type'] === 'error') text-red-800 dark:text-red-200
                                    @elseif($alert['type'] === 'warning') text-yellow-800 dark:text-yellow-200  
                                    @else text-green-800 dark:text-green-200
                                    @endif">
                                    {{ $alert['title'] }}
                                </h3>
                                <p class="text-sm
                                    @if($alert['type'] === 'error') text-red-700 dark:text-red-300
                                    @elseif($alert['type'] === 'warning') text-yellow-700 dark:text-yellow-300
                                    @else text-green-700 dark:text-green-300
                                    @endif">
                                    {{ $alert['message'] }}
                                </p>
                            </div>
                        </div>
                        @if(isset($alert['url']))
                            <a href="{{ $alert['url'] }}" 
                               class="text-sm font-medium
                                   @if($alert['type'] === 'error') text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300
                                   @elseif($alert['type'] === 'warning') text-yellow-600 hover:text-yellow-500 dark:text-yellow-400 dark:hover:text-yellow-300
                                   @else text-green-600 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300
                                   @endif
                                   hover:underline transition-colors duration-200">
                                {{ $alert['action'] }} →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <!-- Cards de Métricas Principais -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total de Empresas -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total de Empresas</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($totalCompanies) }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 flex items-center mt-1">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        +{{ $newCompaniesThisMonth }} este mês
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Subscrições Ativas -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Subscrições Ativas</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($activeSubscriptions) }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                        {{ number_format(($activeSubscriptions / max($totalCompanies, 1)) * 100, 1) }}% das empresas
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Receita Total -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Receita Mensal</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ number_format($totalRevenue, 0, ',', '.') }} MT</p>
                    <p class="text-xs text-purple-600 dark:text-purple-400 mt-1">
                        Subscrições ativas
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Subscrições Expirando -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Expiram em 7 dias</p>
                    <p class="text-3xl font-bold text-zinc-900 dark:text-white">{{ count($expiringSubscriptions) }}</p>
                    <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                        Precisam renovação
                    </p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900/30 p-3 rounded-xl">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Gráfico de Subscrições -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Novas Subscrições</h3>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    Últimos {{ $selectedPeriod }} dias
                </div>
            </div>
            <div class="h-64" wire:ignore>
                <canvas id="subscriptionChart"></canvas>
            </div>
        </div>

        <!-- Gráfico de Receita -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Receita</h3>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">
                    Últimos {{ $selectedPeriod }} dias
                </div>
            </div>
            <div class="h-64" wire:ignore>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Distribuição de Planos -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Distribuição de Planos</h3>
            <div class="h-64" wire:ignore>
                <canvas id="planChart"></canvas>
            </div>
        </div>

        <!-- Empresas por Status -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Status das Empresas</h3>
            <div class="h-64" wire:ignore>
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Subscrições Expirando -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg border border-zinc-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">Expirações Próximas</h3>
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @forelse($expiringSubscriptions as $subscription)
                    <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-700/50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $subscription->company->name }}
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $subscription->plan->name }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">
                                {{ $subscription->expires_at->diffForHumans() }}
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $subscription->expires_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">Nenhuma expiração próxima</h3>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Todas as subscrições estão em dia!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    @livewire('system.audit-widget')
</div>


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('asasa');
    
    // Configuração comum dos charts
    const chartConfig = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    };

    // Chart de Subscrições
    const subscriptionCtx = document.getElementById('subscriptionChart').getContext('2d');
    new Chart(subscriptionCtx, {
        type: 'line',
        data: {
            labels: @json(array_keys($subscriptionTrends)),
            datasets: [{
                data: @json(array_values($subscriptionTrends)),
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            ...chartConfig,
            plugins: {
                ...chartConfig.plugins,
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' novas subscrições';
                        }
                    }
                }
            }
        }
    });

    // Chart de Receita
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($revenueTrends)),
            datasets: [{
                data: @json(array_values($revenueTrends)),
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgb(16, 185, 129)',
                borderWidth: 1
            }]
        },
        options: {
            ...chartConfig,
            plugins: {
                ...chartConfig.plugins,
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('pt-MZ', {
                                style: 'currency',
                                currency: 'MZN'
                            }).format(context.parsed.y);
                        }
                    }
                }
            }
        }
    });

    // Chart de Distribuição de Planos
    const planCtx = document.getElementById('planChart').getContext('2d');
    new Chart(planCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($planDistribution)),
            datasets: [{
                data: @json(array_values($planDistribution)),
                backgroundColor: [
                    'rgba(99, 102, 241, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });

    // Chart de Status das Empresas
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: @json(array_keys($companiesByStatus)),
            datasets: [{
                data: @json(array_values($companiesByStatus)),
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(99, 102, 241, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
});

// Livewire listeners
document.addEventListener('livewire:init', () => {
    Livewire.on('start-realtime', () => {
        // Implementar atualização em tempo real
        console.log('Tempo real ativado');
        // setInterval(() => {
        //     Livewire.dispatch('refreshDashboard');
        // }, 30000); // Atualizar a cada 30 segundos
    });

    Livewire.on('stop-realtime', () => {
        console.log('Tempo real desativado');
        // clearInterval se necessário
    });
});
</script>
@endsection
