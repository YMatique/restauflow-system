{{-- resources/views/livewire/shifts/shift-management.blade.php --}}
<div>
    {{-- <x-layouts.header 
        title="Gestão de Turnos" 
        breadcrumb="Dashboard > Turnos"
        variant="primary"
    /> --}}
     @include('layouts.components.header', [
        'title' => 'Gestão de Turnos',
        'subtitle' => 'Bem-vindo ao sistema',
        'variant' => 'purple',
        'breadcrumb' => 'Dashboard > Turnos',
       
    ])
    
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Logo Section -->
            <div class="text-center mb-8">
                <div class="text-5xl mb-4">🍽️</div>
                <h1 class="text-2xl font-bold text-gray-900">RestaurantPOS</h1>
                <p class="text-gray-600">Sistema de Gestão</p>
            </div>
            
            <!-- User Info -->
            <div class="flex items-center space-x-4 mb-8 p-4 bg-gray-50 rounded-xl">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl">
                    👤
                </div>
                <div>
                    <h3 class="font-semibold">{{ auth()->user()->name }}</h3>
                    <p class="text-sm text-gray-600">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
            
            @if(!$activeShift)
                <!-- No Active Shift -->
                <div class="text-center mb-8">
                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-6 mb-6">
                        <div class="text-4xl mb-4">🔒</div>
                        <h2 class="text-lg font-semibold mb-2">Nenhum turno ativo</h2>
                        <p class="text-gray-600">Sistema bloqueado</p>
                    </div>
                    
                    <x-ui.button 
                        wire:click="$set('showOpenModal', true)"
                        variant="success"
                        size="lg"
                        icon="🚀"
                        class="w-full mb-4"
                    >
                        Abrir Turno
                    </x-ui.button>
                    
                    <x-ui.button 
                        variant="secondary"
                        size="lg"
                        icon="📊"
                        class="w-full"
                    >
                        Histórico de Turnos
                    </x-ui.button>
                </div>
            @else
                <!-- Active Shift -->
                <div class="text-center mb-8">
                    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6 mb-6">
                        <div class="text-4xl mb-4">✅</div>
                        <h2 class="text-lg font-semibold mb-2">Turno em andamento</h2>
                        <p class="text-gray-600">
                            Iniciado: {{ $activeShift->opened_at->format('H:i') }} • 
                            {{ $activeShift->getDurationFormatted() }}
                        </p>
                    </div>
                    
                    <!-- Shift Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600">Fundo Inicial</div>
                            <div class="text-lg font-bold">{{ number_format($activeShift->initial_amount, 0) }} MT</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-sm text-gray-600">Vendas Hoje</div>
                            <div class="text-lg font-bold">{{ number_format($activeShift->total_sales ?? 0, 0) }} MT</div>
                        </div>
                    </div>
                    
                    <x-ui.button 
                        href="{{ route('pos') }}"
                        variant="primary"
                        size="lg"
                        icon="💰"
                        class="w-full mb-4"
                    >
                        Abrir POS
                    </x-ui.button>
                    
                    <x-ui.button 
                        wire:click="$set('showCloseModal', true)"
                        variant="danger"
                        size="lg"
                        icon="🔒"
                        class="w-full"
                    >
                        Fechar Turno
                    </x-ui.button>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Open Shift Modal -->
    <x-ui.modal :show="$showOpenModal" title="Abrir Novo Turno" maxWidth="md">
        <form wire:submit.prevent="openShift" class="p-6">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Valor Inicial do Caixa (MT)
                </label>
                <input 
                    wire:model="openShiftForm.initial_amount"
                    type="number" 
                    step="0.01"
                    class="w-full px-4 py-3 text-center text-2xl font-bold text-green-600 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="2000"
                >
                @error('openShiftForm.initial_amount') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Observações de Abertura
                </label>
                <textarea 
                    wire:model="openShiftForm.opening_notes"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    rows="3"
                    placeholder="Observações sobre a abertura do turno (opcional)..."
                ></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <x-ui.button 
                    type="button"
                    wire:click="$set('showOpenModal', false)"
                    variant="secondary"
                >
                    Cancelar
                </x-ui.button>
                <x-ui.button 
                    type="submit"
                    variant="success"
                    icon="🚀"
                >
                    Iniciar Turno
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
    
    <!-- Close Shift Modal -->
    <x-ui.modal :show="$showCloseModal" title="Fechar Turno" maxWidth="lg">
        <div class="p-6">
            <!-- Shift Summary -->
            <div class="bg-blue-50 rounded-xl p-6 mb-6">
                <h3 class="font-semibold text-blue-900 mb-4">📊 Resumo do Turno</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-4 text-center">
                        <div class="text-sm text-gray-600">Fundo Inicial</div>
                        <div class="text-lg font-bold">{{ number_format($activeShift->initial_amount ?? 0, 0) }} MT</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 text-center">
                        <div class="text-sm text-gray-600">Vendas Dinheiro</div>
                        <div class="text-lg font-bold">{{ number_format($activeShift->cash_sales ?? 0, 0) }} MT</div>
                    </div>
                </div>
                
                <div class="bg-white border-2 border-green-500 rounded-lg p-4 text-center mt-4">
                    <div class="text-green-600 font-semibold mb-1">💎 VALOR ESPERADO NO CAIXA</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ number_format(($activeShift->initial_amount ?? 0) + ($activeShift->cash_sales ?? 0), 0) }} MT
                    </div>
                </div>
            </div>
            
            <form wire:submit.prevent="closeShift">
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Contagem Real do Caixa (MT)
                    </label>
                    <input 
                        wire:model.live="closeShiftForm.final_amount"
                        type="number" 
                        step="0.01"
                        class="w-full px-4 py-3 text-center text-2xl font-bold text-red-600 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="0.00"
                        required
                    >
                    @error('closeShiftForm.final_amount') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Retiradas de Caixa (Sangrias)
                    </label>
                    <input 
                        wire:model="closeShiftForm.withdrawals"
                        type="number" 
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="0.00"
                    >
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Observações do Fechamento *
                    </label>
                    <textarea 
                        wire:model="closeShiftForm.closing_notes"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        rows="3"
                        placeholder="Descreva qualquer ocorrência durante o turno, diferenças encontradas, problemas técnicos, etc..."
                        required
                    ></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <x-ui.button 
                        type="button"
                        wire:click="$set('showCloseModal', false)"
                        variant="secondary"
                    >
                        Cancelar
                    </x-ui.button>
                    <x-ui.button 
                        type="submit"
                        variant="danger"
                        icon="🔒"
                    >
                        Fechar Turno
                    </x-ui.button>
                </div>
            </form>
        </div>
    </x-ui.modal>
</div>