{{-- resources/views/livewire/p-o-s/partials/table-modal.blade.php --}}
@if($showTableModal)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="closeTableModal">
    <div class="bg-white rounded-xl max-w-5xl w-full max-h-[85vh] overflow-y-auto shadow-2xl">
        <!-- Modal Header -->
        <div class="bg-white border-b border-gray-200 p-6 rounded-t-xl">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Selecionar Mesa</h2>
                    <p class="text-gray-600 mt-1">Escolha uma mesa para o pedido</p>
                </div>
                <button wire:click="closeTableModal" 
                        class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="p-6">
            <!-- Quick Options -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex space-x-3">
                    <button wire:click="selectTable(null)"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                        🥤 Balcão/Takeaway
                    </button>
                    <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors font-medium">
                        🚚 Delivery
                    </button>
                </div>
                
                <!-- Legend -->
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span>Disponível</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                        <span>Ocupada</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span>Reservada</span>
                    </div>
                </div>
            </div>

            <!-- Tables Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @forelse($tables as $table)
                <div wire:click="selectTable({{ $table->id }})"
                     class="bg-white border-2 rounded-lg p-4 cursor-pointer transition-all hover:shadow-md 
                            {{ $table->isAvailable() ? 'border-green-200 hover:border-green-400' : ($table->status === 'occupied' ? 'border-red-200 opacity-60 cursor-not-allowed' : 'border-yellow-200 opacity-60 cursor-not-allowed') }}
                            {{ $currentTable?->id === $table->id ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-200' : '' }}"
                     {{ !$table->isAvailable() ? '' : '' }}>
                    
                    <!-- Status Indicator -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-3 h-3 rounded-full 
                                    {{ $table->isAvailable() ? 'bg-green-500' : ($table->status === 'occupied' ? 'bg-red-500' : 'bg-yellow-500') }}">
                        </div>
                        @if($currentTable?->id === $table->id)
                            <div class="text-blue-600 font-bold text-sm">✓</div>
                        @endif
                    </div>
                    
                    <!-- Table Visual -->
                    <div class="flex justify-center mb-3">
                        <div class="relative">
                            @if($table->shape === 'round')
                                <div class="w-12 h-12 bg-gray-100 border-2 border-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-gray-700">{{ $table->name }}</span>
                                </div>
                            @elseif($table->shape === 'square')
                                <div class="w-12 h-12 bg-gray-100 border-2 border-gray-300 rounded-lg flex items-center justify-center">
                                    <span class="text-sm font-bold text-gray-700">{{ $table->name }}</span>
                                </div>
                            @else
                                <div class="w-14 h-10 bg-gray-100 border-2 border-gray-300 rounded-lg flex items-center justify-center">
                                    <span class="text-sm font-bold text-gray-700">{{ $table->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Table Info -->
                    <div class="text-center">
                        <div class="font-semibold text-gray-900 text-sm mb-1">Mesa {{ $table->name }}</div>
                        <div class="text-xs text-gray-600">
                            👥 {{ $table->seats }} lugares
                        </div>
                        @if($table->location)
                            <div class="text-xs text-gray-500 mt-1">
                                📍 {{ $table->location }}
                            </div>
                        @endif
                        
                        @if(!$table->isAvailable())
                            <div class="text-xs font-medium mt-2 
                                        {{ $table->status === 'occupied' ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ $table->status === 'occupied' ? '🔴 Ocupada' : '🟡 Reservada' }}
                            </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                        🍽️
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">Nenhuma mesa configurada</h3>
                    <p class="text-gray-500">Configure as mesas no sistema de gestão</p>
                </div>
                @endforelse
            </div>

            <!-- Selected Table Info -->
            @if($currentTable)
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-blue-900">✅ Mesa Selecionada</h4>
                        <div class="text-sm text-blue-700 mt-1">
                            Mesa {{ $currentTable->name }} • {{ $currentTable->seats }} lugares
                            @if($currentTable->location)
                                • {{ $currentTable->location }}
                            @endif
                        </div>
                    </div>
                    <button wire:click="closeTableModal"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                        Continuar
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif