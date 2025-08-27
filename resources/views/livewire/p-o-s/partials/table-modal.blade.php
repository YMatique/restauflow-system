{{-- resources/views/livewire/p-o-s/partials/table-modal.blade.php --}}
@if($showTableModal)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="closeTableModal">
    <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 text-white p-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">🍽️ Selecionar Mesa</h2>
                    <p class="text-purple-100">Escolha uma mesa para o pedido</p>
                </div>
                <button wire:click="closeTableModal" class="text-white hover:bg-white/20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Quick Options -->
            <div class="flex space-x-3 mb-6">
                <button wire:click="selectTable(null)"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    🥤 Balcão/Takeaway
                </button>
                <button class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                    🚚 Delivery
                </button>
                <div class="flex-1"></div>
                <div class="text-sm text-gray-600 flex items-center">
                    <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span> Disponível
                    <span class="w-3 h-3 bg-red-500 rounded-full mr-2 ml-4"></span> Ocupada
                    <span class="w-3 h-3 bg-yellow-500 rounded-full mr-2 ml-4"></span> Reservada
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @forelse($tables as $table)
                <button wire:click="selectTable({{ $table->id }})"
                        class="table-card {{ $table->isAvailable() ? 'available' : ($table->status === 'occupied' ? 'occupied' : 'reserved') }}
                               {{ $currentTable?->id === $table->id ? 'selected' : '' }}"
                        {{ !$table->isAvailable() ? 'disabled' : '' }}>
                    
                    <!-- Table Visual -->
                    <div class="table-visual">
                        @if($table->shape === 'round')
                            <div class="table-round"></div>
                        @elseif($table->shape === 'square')
                            <div class="table-square"></div>
                        @else
                            <div class="table-rectangle"></div>
                        @endif
                        
                        <!-- Table Number -->
                        <div class="table-number">
                            {{ $table->name }}
                        </div>
                    </div>
                    
                    <!-- Table Info -->
                    <div class="table-info">
                        <div class="table-name">{{ $table->name }}</div>
                        <div class="table-details">
                            👥 {{ $table->seats }} lugares
                            @if($table->location)
                                • {{ $table->location }}
                            @endif
                        </div>
                        
                        @if(!$table->isAvailable())
                            <div class="table-status">
                                @if($table->status === 'occupied')
                                    🔴 Ocupada
                                @elseif($table->status === 'reserved')
                                    🟡 Reservada
                                @endif
                            </div>
                        @endif
                    </div>
                </button>
                @empty
                <div class="col-span-full text-center py-8">
                    <div class="text-4xl mb-4">🍽️</div>
                    <p class="text-gray-500">Nenhuma mesa configurada</p>
                    <p class="text-sm text-gray-400 mt-1">Configure as mesas nas configurações</p>
                </div>
                @endforelse
            </div>

            <!-- Selected Table Info -->
            @if($currentTable)
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                <h4 class="font-semibold text-blue-800 mb-2">✅ Mesa Selecionada</h4>
                <div class="flex items-center space-x-4">
                    <div>
                        <div class="font-medium">{{ $currentTable->name }}</div>
                        <div class="text-sm text-blue-600">
                            👥 {{ $currentTable->seats }} lugares
                            @if($currentTable->location)
                                • 📍 {{ $currentTable->location }}
                            @endif
                        </div>
                    </div>
                    <div class="flex-1"></div>
                    <button wire:click="closeTableModal"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Continuar ➡️
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table-card {
        @apply p-4 rounded-xl border-2 text-center cursor-pointer transition-all transform hover:scale-105;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .table-card.available {
        @apply border-green-300 bg-green-50 hover:border-green-400 hover:bg-green-100;
    }
    
    .table-card.occupied {
        @apply border-red-300 bg-red-50 cursor-not-allowed opacity-70;
    }
    
    .table-card.reserved {
        @apply border-yellow-300 bg-yellow-50 cursor-not-allowed opacity-70;
    }
    
    .table-card.selected {
        @apply border-blue-500 bg-blue-100 ring-2 ring-blue-300;
    }
    
    .table-visual {
        @apply relative mb-3;
        position: relative;
    }
    
    .table-round {
        @apply bg-white border-2 border-gray-400;
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    
    .table-square {
        @apply bg-white border-2 border-gray-400;
        width: 40px;
        height: 40px;
        border-radius: 6px;
    }
    
    .table-rectangle {
        @apply bg-white border-2 border-gray-400;
        width: 50px;
        height: 30px;
        border-radius: 6px;
    }
    
    .table-number {
        @apply absolute inset-0 flex items-center justify-center font-bold text-sm text-gray-700;
    }
    
    .table-info {
        @apply text-center;
    }
    
    .table-name {
        @apply font-semibold text-gray-800;
    }
    
    .table-details {
        @apply text-xs text-gray-600 mt-1;
    }
    
    .table-status {
        @apply text-xs font-medium mt-2;
    }
    
    .table-card:disabled {
        @apply cursor-not-allowed;
        transform: none !important;
    }
    
    .table-card:disabled:hover {
        transform: none !important;
    }
</style>
@endif