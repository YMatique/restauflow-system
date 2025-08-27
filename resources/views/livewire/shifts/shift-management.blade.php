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
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-gray-600 to-gray-800 text-white p-8 text-center">
                    <div class="text-6xl mb-4">🔒</div>
                    <h2 class="text-2xl font-bold mb-2">Sistema Bloqueado</h2>
                    <p class="text-gray-200">É necessário abrir um turno para usar o sistema</p>
                </div>

                <!-- User Info -->
                <div class="p-8">
                    <div class="flex items-center justify-center space-x-4 mb-8">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="text-center">
                            <h3 class="text-xl font-semibold">{{ auth()->user()->name }}</h3>
                            <p class="text-gray-600">{{ ucfirst(auth()->user()->role) }}</p>
                            <p class="text-sm text-gray-500">{{ auth()->user()->company->name }}</p>
                        </div>
                    </div>

                    <!-- Last Shift Info -->
                    @if($lastShift = auth()->user()->shifts()->latest()->first())
                    <div class="bg-gray-50 rounded-xl p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-2">📊 Último Turno</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <div class="text-gray-600">Data</div>
                                <div class="font-medium">{{ $lastShift->opened_at->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">Duração</div>
                                <div class="font-medium">{{ $lastShift->getDurationFormatted() }}</div>
                            </div>
                            <div>
                                <div class="text-gray-600">Vendas</div>
                                <div class="font-medium">{{ number_format($lastShift->total_sales ?? 0, 0) }} MT</div>
                            </div>
                            <div>
                                <div class="text-gray-600">Pedidos</div>
                                <div class="font-medium">{{ $lastShift->total_orders ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="space-y-4">
                        <button wire:click="$set('showOpenModal', true)"
                                class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-4 px-6 rounded-xl font-semibold text-lg hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105">
                            🚀 Abrir Novo Turno
                        </button>
                        
                        <button wire:click="$set('showHistoryModal', true)"
                                class="w-full bg-gray-200 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                            📊 Histórico de Turnos
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
                    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                        <div class="bg-gradient-to-r from-green-600 to-blue-600 text-white p-6">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h2 class="text-2xl font-bold">✅ Turno Ativo</h2>
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
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <div class="text-2xl text-blue-600 mb-1">💰</div>
                                    <div class="text-sm text-gray-600">Vendas Hoje</div>
                                    <div class="text-xl font-bold text-blue-600">{{ number_format($activeShift->total_sales ?? 0, 0) }} MT</div>
                                </div>
                                
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <div class="text-2xl text-green-600 mb-1">📋</div>
                                    <div class="text-sm text-gray-600">Pedidos</div>
                                    <div class="text-xl font-bold text-green-600">{{ $activeShift->total_orders ?? 0 }}</div>
                                </div>
                                
                                <div class="text-center p-4 bg-purple-50 rounded-lg">
                                    <div class="text-2xl text-purple-600 mb-1">📊</div>
                                    <div class="text-sm text-gray-600">Ticket Médio</div>
                                    <div class="text-xl font-bold text-purple-600">
                                        {{ $activeShift->total_orders > 0 ? number_format($activeShift->total_sales / $activeShift->total_orders, 0) : 0 }} MT
                                    </div>
                                </div>
                                
                                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                    <div class="text-2xl text-yellow-600 mb-1">⚡</div>
                                    <div class="text-sm text-gray-600">Vendas/Hora</div>
                                    <div class="text-xl font-bold text-yellow-600">
                                        {{ $activeShift->getDurationInMinutes() > 0 ? number_format(($activeShift->total_sales / $activeShift->getDurationInMinutes()) * 60, 0) : 0 }} MT
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <a href="{{ route('restaurant.pos') }}" 
                                   class="bg-gradient-to-r from-blue-600 to-purple-600 text-white py-4 px-6 rounded-xl font-semibold text-center hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105">
                                    💻 Abrir POS
                                </a>
                                
                                <button wire:click="$set('showCloseModal', true)"
                                        class="bg-gradient-to-r from-red-600 to-pink-600 text-white py-4 px-6 rounded-xl font-semibold hover:from-red-700 hover:to-pink-700 transition-all">
                                    🔒 Fechar Turno
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Chart -->
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-bold mb-4">📈 Vendas por Hora</h3>
                        <div class="h-64 flex items-end justify-between space-x-1">
                            @php
                                $hourlySales = $activeShift->getHourlySales();
                                $maxSales = max(array_column($hourlySales, 'total_amount')) ?: 1;
                            @endphp
                            
                            @foreach($hourlySales as $hour)
                                @if($hour['total_amount'] > 0)
                                <div class="flex flex-col items-center">
                                    <div class="bg-blue-600 rounded-t" 
                                         style="height: {{ ($hour['total_amount'] / $maxSales) * 200 }}px; width: 20px;"
                                         title="{{ $hour['hour'] }}: {{ number_format($hour['total_amount'], 0) }} MT">
                                    </div>
                                    <div class="text-xs mt-1 rotate-45 origin-left">{{ $hour['hour'] }}</div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="space-y-6">
                    <!-- Cash Info -->
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-bold mb-4">💵 Controle de Caixa</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fundo Inicial:</span>
                                <span class="font-semibold">{{ number_format($activeShift->initial_amount, 0) }} MT</span>
                            </div>
                            
                        </div>
                    </div>

                    <!-- Recent Sales -->
                    <div class="bg-white rounded-2xl shadow-xl p-6">
                        <h3 class="text-lg font-bold mb-4">🕒 Últimas Vendas</h3>
                        <div class="space-y-3 max-h-48 overflow-y-auto">
                            @forelse($activeShift->sales()->latest()->limit(5)->get() as $sale)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                <div>
                                    <div class="font-medium text-sm">#{{ $sale->invoice_number }}</div>
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
        <div class="bg-white rounded-2xl max-w-md w-full">
            <div class="bg-gradient-to-r from-green-600 to-blue-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">🚀 Abrir Novo Turno</h2>
                <p class="text-green-100">Configure o fundo de caixa inicial</p>
            </div>
            
            <form wire:submit.prevent="openShift" class="p-6">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        💵 Valor Inicial do Caixa (MT)
                    </label>
                    <input type="number" 
                           wire:model="openShiftForm.initial_amount"
                           class="w-full p-3 text-lg font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
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
                                class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300 transition-colors">
                            {{ number_format($amount, 0) }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📝 Observações de Abertura
                    </label>
                    <textarea wire:model="openShiftForm.opening_notes"
                              rows="3"
                              placeholder="Observações sobre a abertura do turno (opcional)..."
                              class="w-full p-3 border border-gray-300 rounded-lg resize-none"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            wire:click="$set('showOpenModal', false)"
                            class="flex-1 py-3 px-4 bg-gray-200 text-gray-800 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    
                    <button type="submit"
                            class="flex-1 py-3 px-4 bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-blue-700 transition-all">
                        🚀 Abrir Turno
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Close Shift Modal -->
    @if($showCloseModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="$set('showCloseModal', false)">
        <div class="bg-white rounded-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-red-600 to-pink-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">🔒 Fechar Turno</h2>
                <p class="text-red-100">Confirme os valores de fechamento</p>
            </div>
            
            <form wire:submit.prevent="closeShift" class="p-6">
                <!-- Shift Summary -->
                <div class="bg-gray-50 rounded-xl p-4 mb-6">
                    <h4 class="font-semibold mb-3">📊 Resumo do Turno</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <div class="text-gray-600">Duração</div>
                            <div class="font-medium">{{ $activeShift->getDurationFormatted() }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Total Vendas</div>
                            <div class="font-medium">{{ number_format($activeShift->total_sales ?? 0, 0) }} MT</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Pedidos</div>
                            <div class="font-medium">{{ $activeShift->total_orders ?? 0 }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600">Ticket Médio</div>
                            <div class="font-medium">
                                {{ $activeShift->total_orders > 0 ? number_format($activeShift->total_sales / $activeShift->total_orders, 0) : 0 }} MT
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        💵 Valor Final do Caixa (MT)
                    </label>
                    <input type="number" 
                           wire:model.live="closeShiftForm.final_amount"
                           class="w-full p-3 text-lg font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                           step="0.01"
                           required>
                    @error('closeShiftForm.final_amount') 
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        💸 Retiradas Totais (MT)
                    </label>
                    <input type="number" 
                           wire:model.live="closeShiftForm.withdrawals"
                           class="w-full p-3 border border-gray-300 rounded-lg"
                           step="0.01"
                           value="{{ $activeShift->withdrawals ?? 0 }}"
                           readonly>
                </div>

                <!-- Calculation Summary -->
                <div class="bg-blue-50 rounded-xl p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 mb-3">🧮 Cálculo de Fechamento</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Fundo Inicial:</span>
                            <span>{{ number_format($activeShift->initial_amount, 0) }} MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>+ Vendas em Dinheiro:</span>
                            <span>{{ number_format($activeShift->getSalesByPaymentMethod()['cash'] ?? 0, 0) }} MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>- Retiradas:</span>
                            <span>{{ number_format($closeShiftForm['withdrawals'] ?? 0, 0) }} MT</span>
                        </div>
                        <hr>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📝 Observações de Fechamento
                    </label>
                    <textarea wire:model="closeShiftForm.closing_notes"
                              rows="3"
                              placeholder="Observações sobre o fechamento do turno (opcional)..."
                              class="w-full p-3 border border-gray-300 rounded-lg resize-none"></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            wire:click="$set('showCloseModal', false)"
                            class="flex-1 py-3 px-4 bg-gray-200 text-gray-800 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    
                    <button type="submit"
                            class="flex-1 py-3 px-4 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl font-semibold hover:from-red-700 hover:to-pink-700 transition-all">
                        🔒 Confirmar Fechamento
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Withdrawal Modal -->
    @if($showWithdrawalModal ?? false)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="$set('showWithdrawalModal', false)">
        <div class="bg-white rounded-2xl max-w-md w-full">
            <div class="bg-gradient-to-r from-yellow-600 to-orange-600 text-white p-6 rounded-t-2xl">
                <h2 class="text-2xl font-bold">💸 Registrar Retirada</h2>
                <p class="text-yellow-100">Informações da retirada de caixa</p>
            </div>
            
            <form wire:submit.prevent="addWithdrawal" class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        💵 Valor da Retirada (MT)
                    </label>
                    <input type="number" 
                           wire:model="withdrawalForm.amount"
                           class="w-full p-3 text-lg font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                           step="0.01"
                           required>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        📝 Descrição
                    </label>
                    <textarea wire:model="withdrawalForm.description"
                              rows="3"
                              placeholder="Motivo da retirada..."
                              class="w-full p-3 border border-gray-300 rounded-lg resize-none"
                              required></textarea>
                </div>

                <div class="flex space-x-3">
                    <button type="button" 
                            wire:click="$set('showWithdrawalModal', false)"
                            class="flex-1 py-3 px-4 bg-gray-200 text-gray-800 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    
                    <button type="submit"
                            class="flex-1 py-3 px-4 bg-gradient-to-r from-yellow-600 to-orange-600 text-white rounded-xl font-semibold hover:from-yellow-700 hover:to-orange-700 transition-all">
                        💸 Registrar
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>