{{-- resources/views/livewire/shifts/shift-management.blade.php --}}
<div class=" space-y-8">
    @if(!$activeShift)
        {{-- Sistema Bloqueado --}}
        <div class="max-w-2xl mx-auto">
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                
                <!-- Status Header -->
                <div class="bg-zinc-800 dark:bg-zinc-900 px-6 py-8 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-zinc-700 dark:bg-zinc-800 rounded-full mb-4">
                        <svg class="w-10 h-10 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Sistema Bloqueado</h1>
                    <p class="text-zinc-300">Abra um turno para começar a usar o sistema</p>
                </div>

                <!-- User & Last Shift Info -->
                <div class="p-6 space-y-6">
                    <!-- User Info -->
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-xl font-bold">{{ substr(auth()->user()->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ auth()->user()->name }}</h3>
                            <p class="text-zinc-600 dark:text-zinc-400">{{ ucfirst(auth()->user()->role) }}</p>
                            <p class="text-sm text-zinc-500">{{ auth()->user()->company->name ?? 'Sistema' }}</p>
                        </div>
                    </div>

                    <!-- Last Shift -->
                    @if($lastShift = auth()->user()->shifts()->latest()->first())
                    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-4 h-4 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="font-medium text-zinc-800 dark:text-zinc-200">Último Turno</span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Data:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-1">{{ $lastShift->opened_at->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Duração:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-1">{{ $lastShift->getDurationFormatted() }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Vendas:</span>
                                <span class="font-medium text-green-600 dark:text-green-400 ml-1">{{ number_format($lastShift->total_sales ?? 0, 0) }} MT</span>
                            </div>
                            <div>
                                <span class="text-zinc-500 dark:text-zinc-400">Pedidos:</span>
                                <span class="font-medium text-zinc-900 dark:text-zinc-100 ml-1">{{ $lastShift->total_orders ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Actions -->
                    <div class="space-y-3">
                        <button wire:click="$set('showOpenModal', true)"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Abrir Novo Turno
                        </button>
                        
                        <button wire:click="$set('showHistoryModal', true)"
                                class="w-full bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Histórico de Turnos
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- Turno Ativo --}}
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            
            <!-- Status do Turno -->
            <div class="xl:col-span-3">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    
                    <!-- Header Ativo -->
                    <div class="bg-green-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div class="text-white">
                                    <h2 class="text-xl font-bold">Turno Ativo</h2>
                                    <p class="text-green-100 text-sm">Iniciado às {{ $activeShift->opened_at->format('H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-white text-right">
                                <div class="text-2xl font-bold">{{ $activeShift->getDurationFormatted() }}</div>
                                <div class="text-green-100 text-sm">Duração</div>
                            </div>
                        </div>
                    </div>

                    <!-- Métricas -->
                    <div class="p-6">
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            
                            <!-- Vendas -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Vendas</p>
                                        <p class="text-lg font-bold text-blue-600">{{ number_format($activeShift->total_sales ?? 0, 0) }} MT</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Pedidos -->
                            <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Pedidos</p>
                                        <p class="text-lg font-bold text-green-600">{{ $activeShift->total_orders ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Ticket Médio -->
                            <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Ticket Médio</p>
                                        <p class="text-lg font-bold text-purple-600">{{ $activeShift->total_orders > 0 ? number_format($activeShift->total_sales / $activeShift->total_orders, 0) : 0 }} MT</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Vendas/Hora -->
                            <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg border border-orange-200 dark:border-orange-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Por Hora</p>
                                        <p class="text-lg font-bold text-orange-600">{{ $activeShift->getDurationInMinutes() > 0 ? number_format(($activeShift->total_sales / $activeShift->getDurationInMinutes()) * 60, 0) : 0 }} MT</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <a href="{{ route('restaurant.pos') }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium text-center transition-colors inline-flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Abrir POS
                            </a>
                            
                            <button wire:click="$set('showCloseModal', true)"
                                    class="bg-red-600 hover:bg-red-700 text-white py-3 px-4 rounded-lg font-medium transition-colors inline-flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                Fechar Turno
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Caixa -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="font-semibold text-zinc-900 dark:text-white">Caixa</h3>
                    </div>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-zinc-600 dark:text-zinc-400">Fundo Inicial:</span>
                            <span class="font-medium">{{ number_format($activeShift->initial_amount, 0) }} MT</span>
                        </div>
                    </div>
                </div>

                <!-- Últimas Vendas -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="font-semibold text-zinc-900 dark:text-white">Últimas Vendas</h3>
                    </div>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @forelse($activeShift->sales()->latest()->limit(5)->get() as $sale)
                        <div class="flex justify-between items-center py-2 border-b border-zinc-200 dark:border-zinc-700 last:border-0">
                            <div>
                                <div class="font-medium text-sm">#{{ $sale->invoice_number }}</div>
                                <div class="text-xs text-zinc-600 dark:text-zinc-400">{{ $sale->sold_at->format('H:i') }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold text-green-600">{{ number_format($sale->total, 0) }} MT</div>
                                <div class="text-xs">
                                    @if($sale->payment_method === 'cash')
                                        <svg class="w-3 h-3 inline text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    @elseif($sale->payment_method === 'card')
                                        <svg class="w-3 h-3 inline text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 inline text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-6">
                            <svg class="w-8 h-8 mx-auto mb-2 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-sm text-zinc-500">Nenhuma venda</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Abrir Turno -->
    @if($showOpenModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-zinc-800 rounded-xl max-w-md w-full border border-zinc-200 dark:border-zinc-700">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Abrir Novo Turno</h2>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm">Configure o valor inicial do caixa</p>
            </div>
            
            <form wire:submit.prevent="openShift" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Valor Inicial (MT)
                    </label>
                    <input type="number" 
                           wire:model="openShiftForm.initial_amount"
                           class="w-full p-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100"
                           step="0.01" required>
                    @error('openShiftForm.initial_amount') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                    
                    <div class="flex gap-2 mt-2">
                        @foreach([500, 1000, 2000, 5000] as $amount)
                        <button type="button" 
                                wire:click="$set('openShiftForm.initial_amount', {{ $amount }})"
                                class="px-3 py-1 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 rounded text-sm transition-colors">
                            {{ number_format($amount, 0) }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Observações
                    </label>
                    <textarea wire:model="openShiftForm.opening_notes" rows="3"
                              class="w-full p-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100"
                              placeholder="Observações opcionais..."></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showOpenModal', false)"
                            class="flex-1 py-2 px-4 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Abrir Turno
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Fechar Turno -->
    @if($showCloseModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-zinc-800 rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto border border-zinc-200 dark:border-zinc-700">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Fechar Turno</h2>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm">Confirme os valores para fechamento</p>
            </div>
            
            <form wire:submit.prevent="closeShift" class="p-6 space-y-6">
                <!-- Resumo -->
                <div class="bg-zinc-50 dark:bg-zinc-900 rounded-lg p-4 border border-zinc-200 dark:border-zinc-700">
                    <h4 class="font-medium mb-3 text-zinc-900 dark:text-white">Resumo do Turno</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-zinc-500">Duração:</span>
                            <span class="font-medium ml-1">{{ $activeShift->getDurationFormatted() }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500">Vendas:</span>
                            <span class="font-medium text-green-600 ml-1">{{ number_format($activeShift->total_sales ?? 0, 0) }} MT</span>
                        </div>
                        <div>
                            <span class="text-zinc-500">Pedidos:</span>
                            <span class="font-medium ml-1">{{ $activeShift->total_orders ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500">Ticket Médio:</span>
                            <span class="font-medium ml-1">{{ $activeShift->total_orders > 0 ? number_format($activeShift->total_sales / $activeShift->total_orders, 0) : 0 }} MT</span>
                        </div>
                    </div>
                </div>

                <div>
                    <input type="number" 
                           wire:model.live="closeShiftForm.final_amount"
                           class="w-full p-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100"
                           step="0.01" required>
                    @error('closeShiftForm.final_amount') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Retiradas Totais (MT)
                    </label>
                    <input type="number" 
                           wire:model.live="closeShiftForm.withdrawals"
                           class="w-full p-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100"
                           step="0.01" readonly
                           value="{{ $activeShift->withdrawals ?? 0 }}">
                </div>

                <!-- Cálculos -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                    <h4 class="font-medium text-blue-800 dark:text-blue-200 mb-3">Cálculo de Fechamento</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Fundo Inicial:</span>
                            <span>{{ number_format($activeShift->initial_amount, 0) }} MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>+ Vendas Dinheiro:</span>
                            <span>{{ number_format($activeShift->getSalesByPaymentMethod()['cash'] ?? 0, 0) }} MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>- Retiradas:</span>
                            <span>{{ number_format($closeShiftForm['withdrawals'] ?? 0, 0) }} MT</span>
                        </div>
                        <hr class="border-zinc-300 dark:border-zinc-600">
                        @php
                            $expected = ($activeShift->initial_amount + ($activeShift->getSalesByPaymentMethod()['cash'] ?? 0)) - ($closeShiftForm['withdrawals'] ?? 0);
                            $difference = ($closeShiftForm['final_amount'] ?? 0) - $expected;
                        @endphp
                        <div class="flex justify-between font-medium">
                            <span>Esperado:</span>
                            <span>{{ number_format($expected, 0) }} MT</span>
                        </div>
                        <div class="flex justify-between font-medium">
                            <span>Informado:</span>
                            <span>{{ number_format($closeShiftForm['final_amount'] ?? 0, 0) }} MT</span>
                        </div>
                        <div class="flex justify-between font-bold {{ $difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span>Diferença:</span>
                            <span>{{ $difference >= 0 ? '+' : '' }}{{ number_format($difference, 0) }} MT</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Observações de Fechamento
                    </label>
                    <textarea wire:model="closeShiftForm.closing_notes" rows="3"
                              class="w-full p-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100"
                              placeholder="Observações opcionais..."></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showCloseModal', false)"
                            class="flex-1 py-2 px-4 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2 px-4 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                        Fechar Turno
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal Retirada -->
    @if($showWithdrawalModal ?? false)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
        <div class="bg-white dark:bg-zinc-800 rounded-xl max-w-md w-full border border-zinc-200 dark:border-zinc-700">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-xl font-bold text-zinc-900 dark:text-white">Registrar Retirada</h2>
                <p class="text-zinc-600 dark:text-zinc-400 text-sm">Informações da retirada de caixa</p>
            </div>
            
            <form wire:submit.prevent="addWithdrawal" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Valor da Retirada (MT)
                    </label>
                    <input type="number" 
                           wire:model="withdrawalForm.amount"
                           class="w-full p-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100"
                           step="0.01" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">
                        Descrição
                    </label>
                    <textarea wire:model="withdrawalForm.description" rows="3"
                              class="w-full p-3 border border-zinc-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100"
                              placeholder="Motivo da retirada..." required></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="$set('showWithdrawalModal', false)"
                            class="flex-1 py-2 px-4 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-lg transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 py-2 px-4 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                        Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>