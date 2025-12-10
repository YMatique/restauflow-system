<div>
    @if($show)
    <div class="fixed inset-0 z-50 overflow-y-auto" wire:ignore.self>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div wire:click="close" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Finalizar Pagamento</h3>
                    <button wire:click="close" class="text-gray-400 hover:text-gray-500">✕</button>
                </div>

                <div class="space-y-4">
                    <!-- Método de Pagamento -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Método de Pagamento</label>
                        <select wire:model.live="payment_method" class="w-full px-3 py-2 border rounded-lg">
                            <option value="cash">Dinheiro</option>
                            <option value="card">Cartão</option>
                            <option value="mpesa">M-Pesa</option>
                        </select>
                    </div>

                    @if($payment_method === 'cash')
                    <!-- Valor Recebido -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Valor Recebido (MT)</label>
                        <input type="number" wire:model.live="received_amount" step="0.01" 
                               class="w-full px-3 py-2 border rounded-lg">
                    </div>

                    <!-- Troco -->
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <div class="flex justify-between">
                            <span>Troco:</span>
                            <span class="font-bold">{{ number_format($change_amount, 2) }} MT</span>
                        </div>
                    </div>
                    @endif

                    <!-- Total -->
                    <div class="p-3 bg-green-50 rounded-lg">
                        <div class="flex justify-between">
                            <span class="font-semibold">Total:</span>
                            <span class="font-bold text-lg">{{ number_format($total_amount, 2) }} MT</span>
                        </div>
                    </div>

                    <!-- Botões -->
                    <div class="flex gap-3 mt-4">
                        <button wire:click="close" class="flex-1 px-4 py-2 bg-gray-200 rounded-lg">
                            Cancelar
                        </button>
                        <button wire:click="processPayment" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>