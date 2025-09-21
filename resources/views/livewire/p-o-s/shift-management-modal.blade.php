<div>
    <!-- Botão do Menu -->
    <div class="relative" x-data="{ open: false }">
        <button 
            @click="open = !open"
            class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div 
            x-show="open"
            @click.away="open = false"
            x-transition
            class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border z-50"
        >
            <div class="p-3 border-b">
                <div class="text-sm font-medium">Gestão de Caixa</div>
                <div class="text-xs text-gray-500">
                    Saldo: {{ number_format($currentCashBalance, 2) }} MT
                </div>
            </div>

            <div class="p-2">
                <button 
                    wire:click="openWithdrawalModal"
                    @click="open = false"
                    class="w-full text-left px-3 py-2 hover:bg-gray-100 rounded-lg text-sm"
                >
                    💰 Registrar Retirada
                </button>
                
                <button 
                    wire:click="openCloseShiftModal"
                    @click="open = false"
                    class="w-full text-left px-3 py-2 hover:bg-gray-100 rounded-lg text-sm text-red-600"
                >
                    🔒 Fechar Turno
                </button>
            </div>

            <!-- Recent Movements -->
            @if($recentMovements->count() > 0)
                <div class="p-3 border-t">
                    <div class="text-xs font-medium text-gray-500 mb-2">Últimos Movimentos</div>
                    @foreach($recentMovements as $movement)
                        <div class="text-xs py-1 flex justify-between">
                            <span class="truncate">{{ $movement->description }}</span>
                            <span @class([
                                'font-medium',
                                'text-green-600' => $movement->type === 'in',
                                'text-red-600' => $movement->type === 'out'
                            ])>
                                {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->amount, 2) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Retirada -->
    @if($showWithdrawal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div 
                wire:click="closeWithdrawalModal"
                class="fixed inset-0 bg-gray-500 bg-opacity-75"
            ></div>

            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                <h3 class="text-xl font-bold mb-4">Registrar Retirada</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor (MT)
                        </label>
                        <input 
                            type="number" 
                            wire:model="withdrawalForm.amount"
                            step="0.01"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                        @error('withdrawalForm.amount')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Descrição
                        </label>
                        <input 
                            type="text" 
                            wire:model="withdrawalForm.description"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Ex: Troco, Despesas..."
                        >
                        @error('withdrawalForm.description')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button 
                            wire:click="closeWithdrawalModal"
                            class="flex-1 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300"
                        >
                            Cancelar
                        </button>
                        <button 
                            wire:click="registerWithdrawal"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        >
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal de Fechar Turno -->
    @if($showCloseShift)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div 
                wire:click="closeCloseShiftModal"
                class="fixed inset-0 bg-gray-500 bg-opacity-75"
            ></div>

            <div class="relative bg-white rounded-lg shadow-xl w-full max-w-lg p-6">
                <h3 class="text-xl font-bold mb-4">Fechar Turno</h3>

                <!-- Resumo do Turno -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold mb-3">Resumo do Turno</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Valor Inicial:</span>
                            <span class="font-medium">{{ number_format($activeShift->initial_amount, 2) }} MT</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Vendas em Dinheiro:</span>
                            <span class="font-medium text-green-600">
                                +{{ number_format($activeShift->getSalesByPaymentMethod()['cash'] ?? 0, 2) }} MT
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Retiradas:</span>
                            <span class="font-medium text-red-600">
                                -{{ number_format($activeShift->withdrawals ?? 0, 2) }} MT
                            </span>
                        </div>
                        <div class="flex justify-between pt-2 border-t">
                            <span class="font-semibold">Esperado em Caixa:</span>
                            <span class="font-bold text-lg">{{ number_format($closeShiftForm['final_amount'], 2) }} MT</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Valor Real em Caixa (MT)
                        </label>
                        <input 
                            type="number" 
                            wire:model="closeShiftForm.final_amount"
                            step="0.01"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                        >
                        @error('closeShiftForm.final_amount')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Observações (opcional)
                        </label>
                        <textarea 
                            wire:model="closeShiftForm.closing_notes"
                            rows="3"
                            class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="Observações sobre o fechamento..."
                        ></textarea>
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button 
                            wire:click="closeCloseShiftModal"
                            class="flex-1 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300"
                        >
                            Cancelar
                        </button>
                        <button 
                            wire:click="closeShift"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                        >
                            Fechar Turno
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>