{{-- resources/views/livewire/p-o-s/partials/payment-modal.blade.php --}}
@if($showPaymentModal)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="closePaymentModal">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-green-600 to-blue-600 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">💰 Finalizar Venda</h2>
                    <p class="text-green-100">Mesa: {{ $currentTable?->name ?? 'Balcão' }}</p>
                </div>
                <button wire:click="closePaymentModal" class="text-white hover:bg-white/20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Cart Summary -->
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">📋 Resumo do Pedido</h3>
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @foreach($cart as $cartKey => $item)
                    <div class="flex justify-between text-sm">
                        <span>{{ $item['quantity'] }}x {{ $item['product_name'] }}</span>
                        <span class="font-medium">{{ number_format($item['total_price'], 0) }} MT</span>
                    </div>
                    @endforeach
                </div>
                
                <!-- Totals Calculation -->
                <div class="border-t pt-3 mt-3 space-y-1">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span>{{ number_format(collect($cart)->sum('total_price'), 0) }} MT</span>
                    </div>
                    
                    @if($paymentForm['service_charge'] > 0)
                    <div class="flex justify-between">
                        <span>Taxa de Serviço:</span>
                        <span>{{ number_format($paymentForm['service_charge'], 0) }} MT</span>
                    </div>
                    @endif
                    
                    @if($paymentForm['discount_amount'] > 0)
                    <div class="flex justify-between text-red-600">
                        <span>Desconto:</span>
                        <span>-{{ number_format($paymentForm['discount_amount'], 0) }} MT</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between font-bold text-lg border-t pt-2">
                        <span>TOTAL:</span>
                        <span class="text-green-600">{{ number_format($paymentForm['total_amount'], 0) }} MT</span>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <form wire:submit.prevent="processPayment">
                <!-- Payment Method Selection -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
                    <label class="payment-method {{ $paymentForm['payment_method'] === 'cash' ? 'selected' : '' }}">
                        <input type="radio" wire:model.live="paymentForm.payment_method" value="cash" class="hidden">
                        <div class="p-4 rounded-xl border-2 text-center cursor-pointer transition-all
                                    {{ $paymentForm['payment_method'] === 'cash' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <div class="text-2xl mb-2">💵</div>
                            <div class="text-sm font-medium">Dinheiro</div>
                        </div>
                    </label>

                    <label class="payment-method {{ $paymentForm['payment_method'] === 'card' ? 'selected' : '' }}">
                        <input type="radio" wire:model.live="paymentForm.payment_method" value="card" class="hidden">
                        <div class="p-4 rounded-xl border-2 text-center cursor-pointer transition-all
                                    {{ $paymentForm['payment_method'] === 'card' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <div class="text-2xl mb-2">💳</div>
                            <div class="text-sm font-medium">Cartão</div>
                        </div>
                    </label>

                    <label class="payment-method {{ $paymentForm['payment_method'] === 'mbway' ? 'selected' : '' }}">
                        <input type="radio" wire:model.live="paymentForm.payment_method" value="mbway" class="hidden">
                        <div class="p-4 rounded-xl border-2 text-center cursor-pointer transition-all
                                    {{ $paymentForm['payment_method'] === 'mbway' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <div class="text-2xl mb-2">📱</div>
                            <div class="text-sm font-medium">MB Way</div>
                        </div>
                    </label>

                    <label class="payment-method {{ $paymentForm['payment_method'] === 'mpesa' ? 'selected' : '' }}">
                        <input type="radio" wire:model.live="paymentForm.payment_method" value="mpesa" class="hidden">
                        <div class="p-4 rounded-xl border-2 text-center cursor-pointer transition-all
                                    {{ $paymentForm['payment_method'] === 'mpesa' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <div class="text-2xl mb-2">📲</div>
                            <div class="text-sm font-medium">M-Pesa</div>
                        </div>
                    </label>

                    <label class="payment-method {{ $paymentForm['payment_method'] === 'transfer' ? 'selected' : '' }}">
                        <input type="radio" wire:model.live="paymentForm.payment_method" value="transfer" class="hidden">
                        <div class="p-4 rounded-xl border-2 text-center cursor-pointer transition-all
                                    {{ $paymentForm['payment_method'] === 'transfer' ? 'border-purple-500 bg-purple-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <div class="text-2xl mb-2">🏦</div>
                            <div class="text-sm font-medium">Transferência</div>
                        </div>
                    </label>

                    <label class="payment-method {{ $paymentForm['payment_method'] === 'mixed' ? 'selected' : '' }}">
                        <input type="radio" wire:model.live="paymentForm.payment_method" value="mixed" class="hidden">
                        <div class="p-4 rounded-xl border-2 text-center cursor-pointer transition-all
                                    {{ $paymentForm['payment_method'] === 'mixed' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <div class="text-2xl mb-2">🔄</div>
                            <div class="text-sm font-medium">Misto</div>
                        </div>
                    </label>
                </div>

                <!-- Mixed Payment Details -->
                @if($showMixedPayment)
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                    <h4 class="font-semibold mb-3 text-yellow-800">🔄 Pagamento Misto</h4>
                    <div class="space-y-3">
                        @foreach($mixedPayments as $index => $payment)
                        <div class="flex items-center space-x-3">
                            <select wire:model="mixedPayments.{{ $index }}.method" 
                                    class="flex-1 p-2 border border-gray-300 rounded-lg">
                                <option value="cash">💵 Dinheiro</option>
                                <option value="card">💳 Cartão</option>
                                <option value="mbway">📱 MB Way</option>
                                <option value="mpesa">📲 M-Pesa</option>
                            </select>
                            <input type="number" 
                                   wire:model.live="mixedPayments.{{ $index }}.amount"
                                   placeholder="Valor"
                                   class="flex-1 p-2 border border-gray-300 rounded-lg"
                                   step="0.01">
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-3 text-right">
                        <span class="text-sm text-gray-600">
                            Total Misto: {{ number_format(collect($mixedPayments)->sum('amount'), 0) }} MT
                        </span>
                    </div>
                </div>
                @endif

                <!-- Payment Amount (for cash/single payments) -->
                @if(!$showMixedPayment)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $paymentForm['payment_method'] === 'cash' ? '💵 Valor Recebido' : '💳 Valor a Cobrar' }}
                        </label>
                        <input type="number" 
                               wire:model.live="paymentForm.received_amount"
                               class="w-full p-3 text-lg font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               step="0.01"
                               {{ $paymentForm['payment_method'] !== 'cash' ? 'readonly' : '' }}>
                    </div>

                    @if($paymentForm['payment_method'] === 'cash' && $paymentForm['change_amount'] > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">🔄 Troco</label>
                        <div class="w-full p-3 text-lg font-bold bg-green-100 text-green-800 rounded-lg">
                            {{ number_format($paymentForm['change_amount'], 0) }} MT
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Additional Options -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">👥 N° Pessoas</label>
                        <input type="number" 
                               wire:model.live="paymentForm.customer_count"
                               min="1"
                               class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">⚡ Taxa Serviço</label>
                        <input type="number" 
                               wire:model.live="paymentForm.service_charge"
                               step="0.01"
                               placeholder="0.00"
                               class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">🏷️ Desconto</label>
                        <input type="number" 
                               wire:model.live="paymentForm.discount_amount"
                               step="0.01"
                               placeholder="0.00"
                               class="w-full p-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">📝 Observações</label>
                    <textarea wire:model="paymentForm.notes"
                              rows="3"
                              placeholder="Observações sobre a venda (opcional)..."
                              class="w-full p-3 border border-gray-300 rounded-lg resize-none"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button type="button" 
                            wire:click="closePaymentModal"
                            class="flex-1 py-3 px-4 bg-gray-200 text-gray-800 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                        ❌ Cancelar
                    </button>
                    
                    <button type="submit"
                            class="flex-1 py-3 px-4 bg-gradient-to-r from-green-600 to-blue-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-blue-700 transition-all transform hover:scale-105">
                        ✅ Confirmar Pagamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .payment-method input[type="radio"]:checked + div {
        transform: scale(0.95);
    }
</style>
@endif