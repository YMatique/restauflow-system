{{-- resources/views/livewire/shifts/shift-management.blade.php --}}
<div>
    @include('layouts.components.header', [
        'title' => 'Gestão de Turnos',
        'subtitle' => 'Controle de caixa e turnos',
        'variant' => 'primary',
        'breadcrumb' => 'Dashboard > Turnos'
    ])
    
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(!$activeShift)
            {{-- No Active Shift --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gray-800 text-white p-8 text-center">
                    <div class="text-6xl mb-4">🔒</div>
                    <h2 class="text-2xl font-bold mb-2">Sistema Bloqueado</h2>
                    <p class="text-gray-300">É necessário abrir um turno para usar o sistema</p>
                </div>

                <!-- User Info -->
                <div class="p-8">
                    <div class="flex items-center justify-center space-x-4 mb-8">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="text-center">
                            <h3 class="text-xl font-semibold text-gray-900">{{ auth()->user()->name }}</h3>
                            <p class="text-gray-600 capitalize">{{ auth()->user()->role }}</p>
                            <p class="text-sm text-gray-500">{{ auth()->user()->company->name }}</p>
                        </div>
                    </div>

                    <!-- Last Shift Info -->
                    @if($lastShift = auth()->user()->shifts()->latest()->first())
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <span class="text-blue-600 mr-2">📊</span>
                            Último Turno
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <div class="text-gray-600">Data</div>
                                <div class="font-medium text-gray-900">{{ $lastShift->opened_at->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">Duração</div>
                                <div class="font-medium text-gray-900">{{ $lastShift->getDurationFormatted() }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">Vendas</div>
                                <div class="font-medium text-green-600">{{ number_format($lastShift->total_sales ?? 0, 0) }} MT</div>
                            </div>
                            <div>
                                <div class="text-gray-600">Pedidos</div>
                                <div class="font-medium text-gray-900">{{ $lastShift->total_orders ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="space-y-4">
                        <button wire:click="$set('showOpenModal', true)"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 px-6 rounded-xl font-semibold text-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                            {{-- <span>🚀</span> --}}
                            <span>Abrir Novo Turno</span>
                        </button>
                        
                        <button wire:click="$set('showHistoryModal', true)"
                                class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 py-3 px-6 rounded-xl font-semibold transition-colors duration-200 flex items-center justify-center space-x-2">
                            <span>📊</span>
                            <span>Histórico de Turnos</span>
                        </button>
                    </div>
                </div>
            </div>

        @else
            {{-- Active Shift --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Shift Info -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Shift Status Card -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                        <div class="bg-green-600 text-white p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-2xl font-bold flex items-center">
                                        <span class="mr-2">✅</span>
                                        Turno Ativo
                                    </h2>
                                    <p class="text-green-100">Iniciado às {{ $activeShift->opened_at->format('H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-3xl font-bold">{{ $activeShift->getDurationFormatted() }}</div>
                                    <div class="text-sm text-green-100">Duração</div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="p-6">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                <div class="text-center p-4 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="text-2xl text-blue-600 mb-1">💰</div>
                                    <div class="text-sm text-gray-600 mb-1">Vendas Hoje</div>
                                    <div class="text-xl font-bold text-blue-600">{{ number_format($activeShift->total_sales ?? 0, 0) }} MT</div>
                                </div>
                                
                                <div class="text-center p-4 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="text-2xl text-green-600 mb-1">📋</div>
                                    <div class="text-sm text-gray-600 mb-1">Pedidos</div>
                                    <div class="text-xl font-bold text-green-600">{{ $activeShift->total_orders ?? 0 }}</div>
                                </div>
                                
                                <div class="text-center p-4 bg-purple-50 border border-purple-200 rounded-lg">
                                    <div class="text-2xl text-purple-600 mb-1">📊</div>
                                    <div class="text-sm text-gray-600 mb-1">Ticket Médio</div>
                                    <div class="text-xl font-bold text-purple-600">
                                        {{ $activeShift->total_orders > 0 ? number_format($activeShift->total_sales / $activeShift->total_orders, 0) : 0 }} MT
                                    </div>
                                </div>
                                
                                <div class="text-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="text-2xl text-yellow-600 mb-1">⚡</div>
                                    <div class="text-sm text-gray-600 mb-1">Vendas/Hora</div>
                                    <div class="text-xl font-bold text-yellow-600">
                                        {{ $activeShift->getDurationInMinutes() > 0 ? number_format(($activeShift->total_sales / $activeShift->getDurationInMinutes()) * 60, 0) : 0 }} MT
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <a href="{{ route('restaurant.pos') }}" 
                                   class="bg-blue-600 hover:bg-blue-700 text-white py-4 px-6 rounded-xl font-semibold text-center transition-colors duration-200 flex items-center justify-center space-x-2">
                                    <span>💻</span>
                                    <span>Abrir POS</span>
                                </a>
                                
                                <button wire:click="$set('showCloseModal', true)"
                                        class="bg-red-600 hover:bg-red-700 text-white py-4 px-6 rounded-xl font-semibold transition-colors duration-200 flex items-center justify-center space-x-2">
                                    <span>🔒</span>
                                    <span>Fechar Turno</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Chart -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <span class="text-blue-600 mr-2">📈</span>
                            Vendas por Hora
                        </h3>
                        <div class="h-64 flex items-end justify-between space-x-1">
                            @php
                                $hourlySales = $activeShift->getHourlySales();
                                $maxSales = max(array_column($hourlySales, 'total_amount')) ?: 1;
                            @endphp
                            
                            @foreach($hourlySales as $hour)
                                @if($hour['total_amount'] > 0)
                                <div class="flex flex-col items-center">
                                    <div class="bg-blue-600 rounded-t hover:bg-blue-700 transition-colors cursor-pointer" 
                                         style="height: {{ ($hour['total_amount'] / $maxSales) * 200 }}px; width: 20px;"
                                         title="{{ $hour['hour'] }}: {{ number_format($hour['total_amount'], 0) }} MT">
                                    </div>
                                    <div class="text-xs mt-1 rotate-45 origin-left text-gray-600">{{ $hour['hour'] }}</div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <!-- Cash Info -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <span class="text-green-600 mr-2">💵</span>
                            Controle de Caixa
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Fundo Inicial:</span>
                                <span class="font-semibold text-gray-900">{{ number_format($activeShift->initial_amount, 0) }} MT</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Vendas Dinheiro:</span>
                                <span class="font-semibold text-green-600">{{ number_format($activeShift->getSalesByPaymentMethod()['cash'] ?? 0, 0) }} MT</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Retiradas:</span>
                                <span class="font-semibold text-red-600">{{ number_format($activeShift->withdrawals ?? 0, 0) }} MT</span>
                            </div>
                        </div>
                        
                        <button wire:click="$set('showWithdrawalModal', true)"
                                class="w-full mt-4 bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                            <span>💸</span>
                            <span>Registrar Retirada</span>
                        </button>
                    </div>

                    <!-- Recent Sales -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-bold mb-4 flex items-center">
                            <span class="text-blue-600 mr-2">🕒</span>
                            Últimas Vendas
                        </h3>
                        <div class="space-y-3 max-h-48 overflow-y-auto">
                            @forelse($activeShift->sales()->latest()->limit(5)->get() as $sale)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <div>
                                    <div class="font-medium text-sm text-gray-900">#{{ $sale->invoice_number }}</div>
                                    <div class="text-xs text-gray-600">
                                        {{ $sale->sold_at->format('H:i') }}
                                        @if($sale->table)
                                            • {{ $sale->table->name }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-semibold text-green-600">{{ number_format($sale->total, 0) }} MT</div>
                                    <div class="text-xs text-gray-500">
                                        @switch($sale->payment_method)
                                            @case('cash') 💵 @break
                                            @case('card') 💳 @break
                                            @case('mbway') 📱 @break
                                            @case('mpesa') 📲 @break
                                            @default 💰
                                        @endswitch
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-gray-500">
                                <div class="text-2xl mb-2">📋</div>
                                <p class="text-sm">Nenhuma venda ainda</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Open Shift Modal -->
    @if($showOpenModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="$set('showOpenModal', false)">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-200">
            <div class="bg-blue-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold flex items-center">
                    {{-- <span class="mr-2">🚀</span> --}}
                    Abrir Novo Turno
                </h2>
                <p class="text-blue-100">Configure o fundo de caixa inicial</p>
            </div>
            
            <form wire:submit.prevent="openShift" class="p-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <span class="mr-2">💵</span>
                        Valor Inicial do Caixa (MT)
                    </label>
                    <input type="number" 
                           wire:model="openShiftForm.initial_amount"
                           class="w-full p-3 text-lg font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           step="0.01"
                           required>
                    @error('openShiftForm.initial_amount') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                    
                    <!-- Quick Amount Buttons -->
                    <div class="flex space-x-2 mt-3">
                        @foreach([500, 1000, 2000, 5000] as $amount)
                        <button type="button" 
                                wire:click="$set('openShiftForm.initial_amount', {{ $amount }})"
                                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm transition-colors border border-gray-200">
                            {{ number_format($amount, 0) }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <span class="mr-2">📝</span>
                        Observações de Abertura
                    </label>
                    <textarea wire:model="openShiftForm.opening_notes"
                              rows="3"
                              placeholder="Observações sobre a abertura do turno (opcional)..."
                              class="w-full p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            wire:click="$set('showOpenModal', false)"
                            class="flex-1 py-3 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-semibold transition-colors duration-200">
                        Cancelar
                    </button>
                    
                    <button type="submit"
                            class="flex-1 py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-colors duration-200 flex items-center justify-center space-x-2">
                        <span>🚀</span>
                        <span>Abrir Turno</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Close Shift Modal -->
    @if($showCloseModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="$set('showCloseModal', false)">
        <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto shadow-2xl border border-gray-200">
            <div class="bg-red-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold flex items-center">
                    <span class="mr-2">🔒</span>
                    Fechar Turno
                </h2>
                <p class="text-red-100">Confirme os valores de fechamento</p>
            </div>
            
            <form wire:submit.prevent="closeShift" class="p-6">
                <!-- Shift Summary -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6">
                    <h4 class="font-semibold mb-3 flex items-center text-gray-800">
                        <span class="text-blue-600 mr-2">📊</span>
                        Resumo do Turno
                    </h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-gray-600">Duração</div>
                            <div class="font-medium text-gray-900">{{ $activeShift->getDurationFormatted() }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Total Vendas</div>
                            <div class="font-medium text-green-600">{{ number_format($activeShift->total_sales ?? 0, 0) }} MT</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Pedidos</div>
                            <div class="font-medium text-gray-900">{{ $activeShift->total_orders ?? 0 }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Ticket Médio</div>
                            <div class="font-medium text-gray-900">
                                {{ $activeShift->total_orders > 0 ? number_format($activeShift->total_sales / $activeShift->total_orders, 0) : 0 }} MT
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <span class="mr-2">💵</span>
                        Valor Final do Caixa (MT)
                    </label>
                    <input type="number" 
                           wire:model.live="closeShiftForm.final_amount"
                           class="w-full p-3 text-lg font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           step="0.01"
                           required>
                    @error('closeShiftForm.final_amount') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <span class="mr-2">💸</span>
                        Retiradas Totais (MT)
                    </label>
                    <input type="number" 
                           wire:model.live="closeShiftForm.withdrawals"
                           class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50"
                           step="0.01"
                           value="{{ $activeShift->withdrawals ?? 0 }}"
                           readonly>
                </div>

                <!-- Calculation Summary -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                        <span class="mr-2">🧮</span>
                        Cálculo de Fechamento
                    </h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fundo Inicial:</span>
                            <span class="font-medium">{{ number_format($activeShift->initial_amount, 0) }} MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">+ Vendas em Dinheiro:</span>
                            <span class="font-medium text-green-600">{{ number_format($activeShift->getSalesByPaymentMethod()['cash'] ?? 0, 0) }} MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">- Retiradas:</span>
                            <span class="font-medium text-red-600">{{ number_format($closeShiftForm['withdrawals'] ?? 0, 0) }} MT</span>
                        </div>
                        <div class="border-t border-blue-200 pt-2"></div>
                        <div class="flex justify-between font-bold">
                            <span>Valor Esperado:</span>
                            <span>{{ number_format(($activeShift->initial_amount + ($activeShift->getSalesByPaymentMethod()['cash'] ?? 0)) - ($closeShiftForm['withdrawals'] ?? 0), 0) }} MT</span>
                        </div>
                        <div class="flex justify-between font-bold">
                            <span>Valor Informado:</span>
                            <span>{{ number_format($closeShiftForm['final_amount'] ?? 0, 0) }} MT</span>
                        </div>
                        @php
                            $expected = ($activeShift->initial_amount + ($activeShift->getSalesByPaymentMethod()['cash'] ?? 0)) - ($closeShiftForm['withdrawals'] ?? 0);
                            $difference = ($closeShiftForm['final_amount'] ?? 0) - $expected;
                        @endphp
                        <div class="flex justify-between font-bold {{ $difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span>Diferença:</span>
                            <span>{{ $difference >= 0 ? '+' : '' }}{{ number_format($difference, 0) }} MT</span>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <span class="mr-2">📝</span>
                        Observações de Fechamento
                    </label>
                    <textarea wire:model="closeShiftForm.closing_notes"
                              rows="3"
                              placeholder="Observações sobre o fechamento do turno (opcional)..."
                              class="w-full p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            wire:click="$set('showCloseModal', false)"
                            class="flex-1 py-3 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-semibold transition-colors duration-200">
                        Cancelar
                    </button>
                    
                    <button type="submit"
                            class="flex-1 py-3 px-4 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold transition-colors duration-200 flex items-center justify-center space-x-2">
                        <span>🔒</span>
                        <span>Confirmar Fechamento</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Withdrawal Modal -->
    @if($showWithdrawalModal ?? false)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="$set('showWithdrawalModal', false)">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-gray-200">
            <div class="bg-yellow-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold flex items-center">
                    <span class="mr-2">💸</span>
                    Registrar Retirada
                </h2>
                <p class="text-yellow-100">Informações da retirada de caixa</p>
            </div>
            
            <form wire:submit.prevent="addWithdrawal" class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <span class="mr-2">💵</span>
                        Valor da Retirada (MT)
                    </label>
                    <input type="number" 
                           wire:model="withdrawalForm.amount"
                           class="w-full p-3 text-lg font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                           step="0.01"
                           required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <span class="mr-2">📝</span>
                        Descrição
                    </label>
                    <textarea wire:model="withdrawalForm.description"
                              rows="3"
                              placeholder="Motivo da retirada..."
                              class="w-full p-3 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                              required></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            wire:click="$set('showWithdrawalModal', false)"
                            class="flex-1 py-3 px-4 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-semibold transition-colors duration-200">
                        Cancelar
                    </button>
                    
                    <button type="submit"
                            class="flex-1 py-3 px-4 bg-yellow-600 hover:bg-yellow-700 text-white rounded-xl font-semibold transition-colors duration-200 flex items-center justify-center space-x-2">
                        <span>💸</span>
                        <span>Registrar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif






    
</div>
@section('styles')
<style>
    /* resources/css/shift-management.css */

/* Animações suaves para modais */
.shift-modal-enter {
    @apply opacity-0 scale-95;
    transition: all 0.2s ease-out;
}

.shift-modal-enter-active {
    @apply opacity-100 scale-100;
}

.shift-modal-exit {
    @apply opacity-100 scale-100;
    transition: all 0.15s ease-in;
}

.shift-modal-exit-active {
    @apply opacity-0 scale-95;
}

/* Botões de valores rápidos */
.quick-amount-btn {
    @apply px-3 py-1 bg-gray-100 hover:bg-blue-100 border border-gray-200 hover:border-blue-300 text-gray-700 hover:text-blue-700 rounded-lg text-sm transition-all duration-200 font-medium;
}

.quick-amount-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.quick-amount-btn.active {
    @apply bg-blue-100 border-blue-300 text-blue-700;
}

/* Cards de estatísticas com hover */
.stats-card {
    @apply transition-all duration-200;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Indicador de diferença no fechamento */
.difference-positive {
    @apply text-green-600 font-bold flex items-center;
}

.difference-positive::before {
    content: "↗";
    @apply mr-1 text-lg;
}

.difference-negative {
    @apply text-red-600 font-bold flex items-center;
}

.difference-negative::before {
    content: "↘";
    @apply mr-1 text-lg;
}

.difference-neutral {
    @apply text-gray-600 font-bold flex items-center;
}

.difference-neutral::before {
    content: "→";
    @apply mr-1 text-lg;
}

/* Gráfico de vendas por hora */
.sales-chart-bar {
    @apply bg-blue-600 hover:bg-blue-700 transition-colors duration-200 cursor-pointer rounded-t;
    min-height: 4px;
}

.sales-chart-bar:hover {
    transform: scaleY(1.05);
    box-shadow: 0 -2px 8px rgba(59, 130, 246, 0.3);
}

/* Loading states */
.shift-loading {
    @apply animate-pulse;
}

.shift-skeleton {
    @apply bg-gray-200 rounded;
}

/* Status badges */
.status-badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.status-badge.active {
    @apply bg-green-100 text-green-800;
}

.status-badge.closed {
    @apply bg-gray-100 text-gray-800;
}

.status-badge.pending {
    @apply bg-yellow-100 text-yellow-800;
}

/* Tabela de últimas vendas */
.recent-sales-item {
    @apply transition-colors duration-150 hover:bg-gray-50 cursor-pointer;
}

.recent-sales-item:hover {
    background-color: rgba(59, 130, 246, 0.05);
}

/* Responsividade melhorada */
@media (max-width: 768px) {
    .shift-stats-grid {
        @apply grid-cols-1 gap-3;
    }
    
    .shift-stats-card {
        @apply p-3;
    }
    
    .shift-modal {
        @apply mx-2 max-h-[95vh];
    }
    
    .shift-modal-content {
        @apply p-4;
    }
}

/* Efeitos visuais para botões */
.btn-primary-shift {
    @apply bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg;
}

.btn-primary-shift:hover {
    transform: translateY(-1px);
}

.btn-secondary-shift {
    @apply bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2;
}

.btn-secondary-shift:hover {
    transform: translateY(-1px);
}

.btn-danger-shift {
    @apply bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg;
}

.btn-danger-shift:hover {
    transform: translateY(-1px);
}

.btn-warning-shift {
    @apply bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 shadow-md hover:shadow-lg;
}

.btn-warning-shift:hover {
    transform: translateY(-1px);
}

/* Inputs com melhor focus */
.shift-input {
    @apply w-full p-3 border border-gray-300 rounded-lg transition-all duration-200 focus:ring-2 focus:border-blue-500;
}

.shift-input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.shift-input.error {
    @apply border-red-300 focus:ring-red-500 focus:border-red-500;
}

.shift-input.error:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

/* Toast customizado */
.shift-toast {
    @apply fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg border backdrop-blur-sm;
}

.shift-toast.success {
    @apply bg-green-50/90 border-green-200 text-green-800;
}

.shift-toast.error {
    @apply bg-red-50/90 border-red-200 text-red-800;
}

.shift-toast.warning {
    @apply bg-yellow-50/90 border-yellow-200 text-yellow-800;
}

.shift-toast.info {
    @apply bg-blue-50/90 border-blue-200 text-blue-800;
}

/* Overlay para modais */
.shift-overlay {
    @apply fixed inset-0 bg-black/50 backdrop-blur-sm;
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Container principal com scroll suave */
.shift-container {
    @apply max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8;
    scroll-behavior: smooth;
}

/* Cards principais */
.shift-card {
    @apply bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden transition-all duration-200;
}

.shift-card:hover {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Headers de cards */
.shift-card-header {
    @apply p-6 text-white;
}

.shift-card-header.primary {
    @apply bg-blue-600;
}

.shift-card-header.success {
    @apply bg-green-600;
}

.shift-card-header.danger {
    @apply bg-red-600;
}

.shift-card-header.warning {
    @apply bg-yellow-600;
}

.shift-card-header.dark {
    @apply bg-gray-800;
}
</style>
@endsection

@section('scripts')
<script>
    // resources/js/shift-management.js

document.addEventListener('DOMContentLoaded', function() {
    // Inicialização do sistema de gestão de turnos
    initializeShiftManagement();
});

function initializeShiftManagement() {
    // Auto-refresh dos dados do turno a cada 30 segundos
    setInterval(refreshShiftData, 30000);
    
    // Listeners para eventos Livewire
    setupLivewireListeners();
    
    // Configurar atalhos de teclado
    setupKeyboardShortcuts();
    
    // Configurar tooltips
    setupTooltips();
    
    // Configurar validações em tempo real
    setupRealTimeValidation();
}

function refreshShiftData() {
    if (window.Livewire) {
        // Refresh apenas se não houver modal aberto
        const modalsOpen = document.querySelectorAll('.shift-modal:not(.hidden)').length > 0;
        if (!modalsOpen) {
            window.Livewire.dispatch('refreshShiftData');
        }
    }
}

function setupLivewireListeners() {
    // Escutar eventos Livewire
    document.addEventListener('livewire:init', function() {
        
        // Event listener for toast messages
        Livewire.on('toast', (data) => {
            showToast(data.type, data.message, data.duration || 4000);
        });
        
        // Event listener for shift opened
        Livewire.on('shiftOpened', (data) => {
            console.log('Turno aberto:', data.shiftId);
            playNotificationSound('success');
            updateShiftStatus('active');
        });
        
        // Event listener for shift closed
        Livewire.on('shiftClosed', (data) => {
            console.log('Turno fechado:', data.shiftId);
            playNotificationSound('info');
            updateShiftStatus('closed');
        });
        
        // Event listener for modal state changes
        Livewire.on('table-modal-opened', () => {
            console.log('Modal de mesa aberto');
        });
        
        // Event listener for recalculate difference
        Livewire.on('recalculate-difference', () => {
            recalculateClosingDifference();
        });
    });
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl+Alt+O: Abrir turno
        if (e.ctrlKey && e.altKey && e.key === 'o') {
            e.preventDefault();
            const openButton = document.querySelector('[wire\\:click*="showOpenModal"]');
            if (openButton && !openButton.disabled) {
                openButton.click();
            }
        }
        
        // Ctrl+Alt+C: Fechar turno
        if (e.ctrlKey && e.altKey && e.key === 'c') {
            e.preventDefault();
            const closeButton = document.querySelector('[wire\\:click*="showCloseModal"]');
            if (closeButton && !closeButton.disabled) {
                closeButton.click();
            }
        }
        
        // Ctrl+Alt+P: Abrir POS
        if (e.ctrlKey && e.altKey && e.key === 'p') {
            e.preventDefault();
            const posLink = document.querySelector('a[href*="pos"]');
            if (posLink) {
                window.location.href = posLink.href;
            }
        }
        
        // Escape: Fechar modais
        if (e.key === 'Escape') {
            closeAllModals();
        }
    });
}

function setupTooltips() {
    // Configurar tooltips para elementos com título
    const tooltipElements = document.querySelectorAll('[title]');
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function setupRealTimeValidation() {
    // Validação em tempo real para campos monetários
    const moneyInputs = document.querySelectorAll('input[type="number"]');
    moneyInputs.forEach(input => {
        input.addEventListener('input', function() {
            validateMoneyInput(this);
        });
        
        input.addEventListener('blur', function() {
            formatMoneyInput(this);
        });
    });
}

function validateMoneyInput(input) {
    const value = parseFloat(input.value);
    const max = parseFloat(input.getAttribute('max')) || 999999;
    const min = parseFloat(input.getAttribute('min')) || 0;
    
    // Remove classe de erro anterior
    input.classList.remove('error');
    
    // Validar limites
    if (value > max) {
        input.classList.add('error');
        showInputError(input, `Valor máximo permitido: ${formatMoney(max)} MT`);
    } else if (value < min) {
        input.classList.add('error');
        showInputError(input, `Valor mínimo permitido: ${formatMoney(min)} MT`);
    } else {
        hideInputError(input);
    }
}

function formatMoneyInput(input) {
    const value = parseFloat(input.value);
    if (!isNaN(value)) {
        input.value = value.toFixed(2);
    }
}

function showInputError(input, message) {
    // Remove erro anterior
    hideInputError(input);
    
    // Criar elemento de erro
    const errorElement = document.createElement('div');
    errorElement.className = 'input-error text-red-500 text-xs mt-1';
    errorElement.textContent = message;
    errorElement.id = `error-${input.name || 'input'}`;
    
    // Inserir após o input
    input.parentNode.insertBefore(errorElement, input.nextSibling);
}

function hideInputError(input) {
    const errorElement = input.parentNode.querySelector('.input-error');
    if (errorElement) {
        errorElement.remove();
    }
}

function showToast(type, message, duration = 4000) {
    // Criar elemento do toast
    const toast = document.createElement('div');
    toast.className = `shift-toast ${type} animate-slide-in`;
    toast.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                ${getToastIcon(type)}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button class="inline-flex text-gray-400 hover:text-gray-600" onclick="this.parentElement.parentElement.parentElement.remove()">
                    <span class="sr-only">Fechar</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    // Adicionar ao DOM
    document.body.appendChild(toast);
    
    // Auto-remove após duração especificada
    setTimeout(() => {
        toast.classList.add('animate-slide-out');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }, duration);
}

function getToastIcon(type) {
    const icons = {
        success: '<svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
        error: '<svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
        warning: '<svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
        info: '<svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
    };
    return icons[type] || icons.info;
}

function playNotificationSound(type) {
    // Reproduzir som de notificação (se suportado pelo navegador)
    if ('speechSynthesis' in window) {
        const sounds = {
            success: 'ding',
            error: 'buzz',
            warning: 'beep',
            info: 'chime'
        };
        
        // Criar audio context se disponível
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            const frequencies = {
                success: 800,
                error: 300,
                warning: 600,
                info: 500
            };
            
            oscillator.frequency.setValueAtTime(frequencies[type] || 500, audioContext.currentTime);
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioContext.currentTime + 0.2);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.2);
        } catch (e) {
            console.log('Audio notification not available');
        }
    }
}

function updateShiftStatus(status) {
    const statusIndicator = document.querySelector('.shift-status-indicator');
    if (statusIndicator) {
        statusIndicator.className = `shift-status-indicator status-badge ${status}`;
        statusIndicator.textContent = status === 'active' ? 'Ativo' : 'Fechado';
    }
}

function closeAllModals() {
    // Fechar todos os modais Livewire
    if (window.Livewire) {
        window.Livewire.dispatch('closeAllModals');
    }
    
    // Fechar modais HTML tradicionais
    const modals = document.querySelectorAll('.shift-modal');
    modals.forEach(modal => {
        modal.classList.add('hidden');
    });
}

function showTooltip(event) {
    const element = event.target;
    const title = element.getAttribute('title');
    
    if (!title) return;
    
    // Remover título para evitar tooltip padrão
    element.removeAttribute('title');
    element.setAttribute('data-original-title', title);
    
    // Criar tooltip personalizado
    const tooltip = document.createElement('div');
    tooltip.className = 'shift-tooltip absolute z-50 px-2 py-1 text-xs text-white bg-gray-800 rounded shadow-lg';
    tooltip.textContent = title;
    tooltip.id = 'shift-tooltip';
    
    document.body.appendChild(tooltip);
    
    // Posicionar tooltip
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
}

function hideTooltip(event) {
    const element = event.target;
    const originalTitle = element.getAttribute('data-original-title');
    
    if (originalTitle) {
        element.setAttribute('title', originalTitle);
        element.removeAttribute('data-original-title');
    }
    
    // Remover tooltip personalizado
    const tooltip = document.getElementById('shift-tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

function recalculateClosingDifference() {
    // Esta função é chamada quando o valor final muda
    // A lógica principal está no Livewire, mas podemos adicionar feedback visual aqui
    const finalAmountInput = document.querySelector('input[wire\\:model*="final_amount"]');
    if (finalAmountInput) {
        // Adicionar classe de cálculo ativo
        finalAmountInput.classList.add('calculating');
        
        setTimeout(() => {
            finalAmountInput.classList.remove('calculating');
        }, 500);
    }
}

function formatMoney(value) {
    return new Intl.NumberFormat('pt-MZ', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
}

function initializeCharts() {
    // Inicializar gráficos se necessário
    const chartContainer = document.querySelector('.sales-chart');
    if (chartContainer) {
        // Adicionar interatividade aos gráficos
        const bars = chartContainer.querySelectorAll('.sales-chart-bar');
        bars.forEach(bar => {
            bar.addEventListener('mouseenter', function() {
                const tooltip = this.getAttribute('title');
                if (tooltip) {
                    showChartTooltip(this, tooltip);
                }
            });
            
            bar.addEventListener('mouseleave', function() {
                hideChartTooltip();
            });
        });
    }
}

function showChartTooltip(element, text) {
    const tooltip = document.createElement('div');
    tooltip.className = 'chart-tooltip absolute z-50 px-2 py-1 text-xs text-white bg-gray-800 rounded shadow-lg pointer-events-none';
    tooltip.textContent = text;
    tooltip.id = 'chart-tooltip';
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
}

function hideChartTooltip() {
    const tooltip = document.getElementById('chart-tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

// Função para exportar dados (futura implementação)
function exportShiftData(format = 'pdf') {
    if (window.Livewire) {
        window.Livewire.dispatch('exportShiftReport', { format: format });
    }
}

// Função para imprimir resumo do turno
function printShiftSummary() {
    const printContent = document.querySelector('.shift-summary');
    if (printContent) {
        const newWin = window.open('', '_blank');
        newWin.document.write(`
            <html>
                <head>
                    <title>Resumo do Turno</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
                        .stat-item { border: 1px solid #ddd; padding: 10px; border-radius: 5px; }
                        @media print { 
                            body { margin: 0; } 
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    ${printContent.innerHTML}
                </body>
            </html>
        `);
        newWin.document.close();
        newWin.print();
    }
}

// Inicializar funcionalidades adicionais quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    
    // Configurar botões de ação rápida
    const quickActionButtons = document.querySelectorAll('.quick-action-btn');
    quickActionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            handleQuickAction(action);
        });
    });
});

function handleQuickAction(action) {
    switch(action) {
        case 'open-pos':
            window.location.href = '/restaurant/pos';
            break;
        case 'view-reports':
            window.location.href = '/restaurant/reports';
            break;
        case 'export-data':
            exportShiftData();
            break;
        case 'print-summary':
            printShiftSummary();
            break;
        default:
            console.log('Ação não reconhecida:', action);
    }
}

// CSS adicional para animações
const additionalStyles = `
    .animate-slide-in {
        animation: slideIn 0.3s ease-out;
    }
    
    .animate-slide-out {
        animation: slideOut 0.3s ease-in;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .calculating {
        animation: pulse 0.5s ease-in-out;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
`;

// Adicionar estilos ao documento
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);
</script>
@endsection