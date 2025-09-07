{-- resources/views/livewire/stock/stock-management.blade.php --}}
<div>
    <x-layouts.header 
        title="Gestão de Stock" 
        breadcrumb="Dashboard > Stock"
        variant="warning"
    >
        <x-slot:actions>
            <div class="flex items-center space-x-3">
                <x-ui.button 
                    wire:click="openMovementModal('in')"
                    variant="success"
                    size="sm"
                    icon="📦"
                >
                    Entrada
                </x-ui.button>
                <x-ui.button 
                    wire:click="openMovementModal('out')"
                    variant="danger"
                    size="sm"
                    icon="📤"
                >
                    Saída
                </x-ui.button>
            </div>
        </x-slot:actions>
    </x-layouts.header>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-ui.stats-card 
                title="Total de Itens"
                value="{{ $stats['total_items'] }}"
                subtitle="Produtos + Ingredientes"
                icon="📦"
                color="blue"
            />
            
            <x-ui.stats-card 
                title="Stock Baixo"
                value="{{ $stats['low_stock'] }}"
                subtitle="Itens abaixo do mínimo"
                icon="⚠️"
                color="yellow"
            />
            
            <x-ui.stats-card 
                title="Em Falta"
                value="{{ $stats['out_stock'] }}"
                subtitle="Itens zerados"
                icon="❌"
                color="red"
            />
            
            <x-ui.stats-card 
                title="Valor Total"
                value="{{ number_format($stats['total_value'], 0) }} MT"
                subtitle="Valor estimado do stock"
                icon="💰"
                color="green"
            />
        </div>
        
        <!-- Tabs -->
        <x-ui.card padding="none" class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <button 
                        wire:click="$set('activeTab', 'movements')"
                        class="px-6 py-4 text-sm font-medium {{ $activeTab === 'movements' ? 'border-b-2 border-amber-500 text-amber-600 bg-amber-50' : 'text-gray-500 hover:text-gray-700' }}"
                    >
                        📋 Movimentações
                    </button>
                    <button 
                        wire:click="$set('activeTab', 'alerts')"
                        class="px-6 py-4 text-sm font-medium {{ $activeTab === 'alerts' ? 'border-b-2 border-amber-500 text-amber-600 bg-amber-50' : 'text-gray-500 hover:text-gray-700' }}"
                    >
                        🚨 Alertas ({{ count($lowStockAlerts) }})
                    </button>
                </nav>
            </div>
            
            <div class="p-6">
                @if($activeTab === 'movements')
                    <!-- Recent Movements -->
                    <div class="space-y-4">
                        @forelse($recentMovements as $movement)
                            <div class="flex items-center p-4 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $movement->type === 'in' ? 'bg-green-100' : 'bg-red-100' }}">
                                    @if($movement->type === 'in')
                                        📦
                                    @else
                                        📤
                                    @endif
                                </div>
                                
                                <div class="flex-1 ml-4">
                                    <h4 class="font-semibold text-gray-900">
                                        {{ $movement->type === 'in' ? 'Entrada' : 'Saída' }} - {{ $movement->product->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ $movement->reason }} • {{ $movement->user->name }}</p>
                                </div>
                                
                                <div class="text-right">
                                    <div class="font-bold {{ $movement->type === 'in' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }} 
                                        {{ $movement->product->category->name === 'Ingredientes' ? 'kg/L' : 'unid.' }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $movement->date->diffForHumans() }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="text-4xl mb-4">📋</div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhuma movimentação</h3>
                                <p class="text-gray-600">As movimentações de stock aparecerão aqui</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <!-- Stock Alerts -->
                    <div class="space-y-4">
                        @forelse($lowStockAlerts as $product)
                            <div class="flex items-center p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
                                <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
                                    ⚠️
                                </div>
                                
                                <div class="flex-1 ml-4">
                                    <h4 class="font-semibold text-yellow-800">Stock Baixo</h4>
                                    <p class="text-sm text-yellow-700">
                                        {{ $product->name }} - {{ $product->stock_quantity ?? 0 }} unid. (mín: {{ $product->min_level }})
                                    </p>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">{{ $product->category->name }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="text-4xl mb-4">✅</div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum alerta</h3>
                                <p class="text-gray-600">Todos os produtos estão com stock adequado</p>
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>
        </x-ui.card>
    </div>
    
    <!-- Stock Movement Modal -->
    <x-ui.modal :show="$showMovementModal" :title="$movementType === 'in' ? 'Entrada de Stock' : 'Saída de Stock'" maxWidth="md">
        <form wire:submit.prevent="saveMovement" class="p-6 space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Produto *</label>
                <select 
                    wire:model="movementForm.item_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    required
                >
                    <option value="">Selecionar produto</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->stock_quantity ?? 0 }} unid.)</option>
                    @endforeach
                </select>
                @error('movementForm.item_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantidade *</label>
                <input 
                    wire:model="movementForm.quantity"
                    type="number" 
                    step="0.1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    required
                >
                @error('movementForm.quantity') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            @if($movementType === 'in')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fornecedor</label>
                    <input 
                        wire:model="movementForm.supplier"
                        type="text" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Nome do fornecedor"
                    >
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Custo Total</label>
                        <input 
                            wire:model="movementForm.unit_cost"
                            type="number" 
                            step="0.01"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="0.00"
                        >
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nº Fatura</label>
                        <input 
                            wire:model="movementForm.invoice_number"
                            type="text" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                            placeholder="Opcional"
                        >
                    </div>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo da Saída *</label>
                    <select 
                        wire:model="movementForm.reason"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        required
                    >
                        <option value="sale">Venda</option>
                        <option value="loss">Perda/Quebra</option>
                        <option value="expired">Vencido</option>
                        <option value="adjustment">Ajuste</option>
                        <option value="other">Outro</option>
                    </select>
                </div>
            @endif
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                <textarea 
                    wire:model="movementForm.notes"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                    rows="3"
                    placeholder="Observações adicionais..."
                ></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6">
                <x-ui.button 
                    type="button"
                    wire:click="$set('showMovementModal', false)"
                    variant="secondary"
                >
                    Cancelar
                </x-ui.button>
                <x-ui.button 
                    type="submit"
                    :variant="$movementType === 'in' ? 'success' : 'danger'"
                    :loading="$loading"
                >
                    Registrar {{ $movementType === 'in' ? 'Entrada' : 'Saída' }}
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>