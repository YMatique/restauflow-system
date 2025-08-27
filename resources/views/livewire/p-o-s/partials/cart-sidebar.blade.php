{{-- resources/views/livewire/p-o-s/partials/cart-sidebar.blade.php --}}
<!-- Cart Sidebar (Right Panel) -->
<div class="w-80 bg-white border-l-2 border-gray-200 p-4 flex flex-col h-full">
    <!-- Cash Management Widget -->
    @if($activeShift)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-sm font-semibold text-blue-900">💰 Controle de Caixa</h4>
            <div class="text-xs text-blue-600">{{ $activeShift->opened_at->format('H:i') }}</div>
        </div>
        
        <div class="text-center mb-3">
            <div class="text-2xl font-bold text-blue-600">
                {{ number_format($currentCashBalance, 0) }} MT
            </div>
            <div class="text-xs text-blue-700">Saldo atual em caixa</div>
        </div>
        
        <div class="grid grid-cols-2 gap-2">
            <button wire:click="openWithdrawalModal"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs py-2 px-3 rounded-lg font-medium transition-colors">
                💸 Retirada
            </button>
            <button wire:click="openCloseShiftModal"
                    class="bg-red-500 hover:bg-red-600 text-white text-xs py-2 px-3 rounded-lg font-medium transition-colors">
                🔒 Fechar
            </button>
        </div>
    </div>
    @endif

    <!-- Cart Header -->
    <div class="flex justify-between items-center mb-4 pb-3 border-b">
        <div>
            <h3 class="font-bold text-lg">🛒 Carrinho</h3>
            <p class="text-sm text-gray-600">{{ count($cart) }} {{ count($cart) === 1 ? 'item' : 'itens' }}</p>
        </div>
        
        @if(!empty($cart))
        <button wire:click="clearCart" 
                class="text-red-600 hover:bg-red-50 p-2 rounded-lg transition-colors"
                onclick="return confirm('Limpar carrinho?')">
            🗑️
        </button>
        @endif
    </div>

    <!-- Current Table Info -->
    <div class="mb-4">
        @if($currentTable)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-center justify-between">
                <div>
                    <div class="font-semibold text-blue-800">🍽️ {{ $currentTable->name }}</div>
                    <div class="text-xs text-blue-600">{{ $currentTable->seats }} lugares</div>
                </div>
                <button wire:click="openTableModal" 
                        class="text-blue-600 hover:bg-blue-100 p-1 rounded">
                    ✏️
                </button>
            </div>
        @else
            <button wire:click="openTableModal" 
                    class="w-full bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-yellow-800 hover:bg-yellow-100 transition-colors">
                ⚠️ Selecionar Mesa
            </button>
        @endif
    </div>

    <!-- Cart Items -->
    <div class="flex-1 overflow-y-auto scrollbar-thin mb-4">
        @forelse($cart as $cartKey => $item)
            <div class="bg-gray-50 rounded-xl p-3 mb-3 hover:bg-gray-100 transition-colors">
                <!-- Product Info -->
                <div class="flex justify-between items-start mb-2">
                    <div class="flex-1">
                        <h4 class="font-semibold text-sm">{{ $item['product_name'] }}</h4>
                        <p class="text-xs text-gray-600">{{ number_format($item['unit_price'], 0) }} MT cada</p>
                    </div>
                    <button wire:click="updateCartQuantity('{{ $cartKey }}', 0)"
                            class="text-red-500 hover:bg-red-100 p-1 rounded ml-2">
                        ❌
                    </button>
                </div>

                <!-- Quantity Controls -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <button wire:click="updateCartQuantity('{{ $cartKey }}', {{ $item['quantity'] - 1 }})"
                                class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center font-bold">
                            −
                        </button>
                        
                        <div class="w-12 text-center font-bold">
                            {{ number_format($item['quantity'], 0) }}
                        </div>
                        
                        <button wire:click="updateCartQuantity('{{ $cartKey }}', {{ $item['quantity'] + 1 }})"
                                class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-lg flex items-center justify-center font-bold">
                            +
                        </button>
                    </div>
                    
                    <div class="font-bold text-blue-600">
                        {{ number_format($item['total_price'], 0) }} MT
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500">
                <div class="text-4xl mb-4">🛒</div>
                <p class="text-sm">Carrinho vazio</p>
                <p class="text-xs mt-1">Selecione produtos para adicionar</p>
            </div>
        @endforelse
    </div>

    <!-- Cart Summary -->
    @if(!empty($cart))
    <div class="border-t pt-4">
        <!-- Totals -->
        <div class="space-y-2 mb-4">
            <div class="flex justify-between text-sm">
                <span>Subtotal:</span>
                <span>{{ number_format($cartTotal, 0) }} MT</span>
            </div>
            
            <div class="flex justify-between text-sm">
                <span>Itens:</span>
                <span>{{ $cartCount ?? array_sum(array_column($cart, 'quantity')) }}</span>
            </div>
            
            @if(auth()->user()->company->tax_rate > 0)
            <div class="flex justify-between text-sm text-gray-600">
                <span>IVA ({{ auth()->user()->company->tax_rate }}%):</span>
                <span>{{ number_format($cartTotal * auth()->user()->company->tax_rate / 100, 0) }} MT</span>
            </div>
            @endif
            
            <div class="flex justify-between font-bold text-lg border-t pt-2">
                <span>TOTAL:</span>
                <span class="text-green-600">
                    {{ number_format($cartTotal + ($cartTotal * auth()->user()->company->tax_rate / 100), 0) }} MT
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-2">
            <button wire:click="openPaymentModal"
                    class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white py-3 px-4 rounded-xl font-semibold hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105">
                💳 Finalizar Pagamento
            </button>
            
            <div class="grid grid-cols-2 gap-2">
                <button class="bg-gray-200 text-gray-700 py-2 px-3 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                    📄 Conta Separada
                </button>
                <button class="bg-gray-200 text-gray-700 py-2 px-3 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                    🖨️ Pré-Conta
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Cash Movements Widget -->
    @if($activeShift && $recentMovements->count() > 0)
    <div class="mt-4 pt-4 border-t">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">📝 Últimos Movimentos</h4>
        <div class="space-y-2 max-h-32 overflow-y-auto scrollbar-thin">
            @foreach($recentMovements as $movement)
            <div class="flex items-center justify-between text-xs p-2 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-2">
                    <span class="{{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $movement->type === 'in' ? '📈' : '📉' }}
                    </span>
                    <div>
                        <div class="font-medium">{{ Str::limit($movement->description, 20) }}</div>
                        <div class="text-gray-500">{{ $movement->date->format('H:i') }}</div>
                    </div>
                </div>
                <div class="font-bold {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->amount, 0) }} MT
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="mt-4 pt-4 border-t">
        <div class="text-xs text-gray-600 mb-2">Ações Rápidas:</div>
        <div class="grid grid-cols-3 gap-1">
            <button class="p-2 bg-purple-100 text-purple-600 rounded-lg text-xs font-medium hover:bg-purple-200 transition-colors">
                🏷️ Desconto
            </button>
            <button class="p-2 bg-orange-100 text-orange-600 rounded-lg text-xs font-medium hover:bg-orange-200 transition-colors">
                ⚡ Taxa
            </button>
            <button class="p-2 bg-indigo-100 text-indigo-600 rounded-lg text-xs font-medium hover:bg-indigo-200 transition-colors">
                📝 Nota
            </button>
        </div>
    </div>

    <!-- Shift Info (Footer) -->
    @if($activeShift)
    <div class="mt-4 pt-3 border-t text-xs text-gray-500">
        <div class="flex justify-between">
            <span>👤 {{ auth()->user()->name }}</span>
            <span>🕐 {{ now()->format('H:i') }}</span>
        </div>
        <div class="flex justify-between mt-1">
            <span>📊 Turno: {{ $shiftInfo }}</span>
            <span>💰 {{ number_format($activeShift->total_sales ?? 0, 0) }} MT</span>
        </div>
    </div>
    @endif
</div>

<!-- Withdrawal Modal -->
@if($showWithdrawalModal)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="closeWithdrawalModal">
    <div class="bg-white rounded-xl max-w-md w-full">
        <div class="bg-yellow-500 text-white p-4 rounded-t-xl">
            <h2 class="text-lg font-bold">💸 Registrar Retirada</h2>
            <p class="text-yellow-100 text-sm">Informações da retirada de caixa</p>
        </div>
        
        <form wire:submit.prevent="registerWithdrawal" class="p-4">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    💵 Valor da Retirada (MT)
                </label>
                <input type="number" 
                       wire:model="withdrawalForm.amount"
                       class="w-full p-3 text-lg font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                       step="0.01"
                       required>
                @error('withdrawalForm.amount') 
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Descrição
                </label>
                <textarea wire:model="withdrawalForm.description"
                          rows="3"
                          placeholder="Motivo da retirada (ex: troco, despesas, etc.)..."
                          class="w-full p-3 border border-gray-300 rounded-lg resize-none"
                          required></textarea>
                @error('withdrawalForm.description') 
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> 
                @enderror
            </div>

            <div class="flex space-x-3">
                <button type="button" 
                        wire:click="closeWithdrawalModal"
                        class="flex-1 py-3 px-4 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                
                <button type="submit"
                        class="flex-1 py-3 px-4 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium transition-colors">
                    💸 Registrar
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Close Shift Modal -->
@if($showCloseShiftModal)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="closeCloseShiftModal">
    <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="bg-red-500 text-white p-4 rounded-t-xl">
            <h2 class="text-lg font-bold">🔒 Fechar Turno</h2>
            <p class="text-red-100 text-sm">Confirme os valores de fechamento</p>
        </div>
        
        <form wire:submit.prevent="closeShift" class="p-4">
            <!-- Current Cash Info -->
            <div class="bg-blue-50 rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-blue-800 mb-3">💰 Resumo do Caixa</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Fundo Inicial:</span>
                        <span>{{ number_format($activeShift->initial_amount ?? 0, 0) }} MT</span>
                    </div>
                    <div class="flex justify-between">
                        <span>+ Vendas em Dinheiro:</span>
                        <span class="text-green-600">{{ number_format($activeShift->getSalesByPaymentMethod()['cash'] ?? 0, 0) }} MT</span>
                    </div>
                    <div class="flex justify-between">
                        <span>- Retiradas:</span>
                        <span class="text-red-600">{{ number_format($activeShift->withdrawals ?? 0, 0) }} MT</span>
                    </div>
                    <hr>
                    <div class="flex justify-between font-bold text-base">
                        <span>Valor Esperado:</span>
                        <span class="text-blue-600">{{ number_format($currentCashBalance, 0) }} MT</span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
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
                
                <!-- Difference Calculator -->
                @if($closeShiftForm['final_amount'] > 0)
                    @php
                        $difference = $closeShiftForm['final_amount'] - $currentCashBalance;
                    @endphp
                    <div class="mt-2 p-2 rounded-lg text-sm {{ $difference >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        <strong>Diferença: {{ $difference >= 0 ? '+' : '' }}{{ number_format($difference, 0) }} MT</strong>
                        @if(abs($difference) < 0.01)
                            🎯 Caixa conferido!
                        @elseif($difference > 0)
                            📈 Sobra no caixa
                        @else
                            📉 Falta no caixa
                        @endif
                    </div>
                @endif
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    📝 Observações de Fechamento
                </label>
                <textarea wire:model="closeShiftForm.closing_notes"
                          rows="3"
                          placeholder="Observações sobre o fechamento, diferenças encontradas, etc..."
                          class="w-full p-3 border border-gray-300 rounded-lg resize-none"></textarea>
            </div>

            <div class="flex space-x-3">
                <button type="button" 
                        wire:click="closeCloseShiftModal"
                        class="flex-1 py-3 px-4 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                
                <button type="submit"
                        class="flex-1 py-3 px-4 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors">
                    🔒 Fechar Turno
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>