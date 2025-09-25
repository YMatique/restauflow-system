<div>
    <div class="h-screen flex flex-col bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <h1 class="text-2xl font-bold">POS</h1>
                
                @if($currentTable)
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        Mesa: {{ $currentTable->name }}
                    </span>
                @else
                    <button 
                        wire:click="openTableModal"
                        class="px-3 py-1 bg-blue-500 text-white rounded-lg text-sm font-medium"
                    >
                        Selecionar Mesa
                    </button>
                @endif
            </div>

            <div class="flex items-center gap-4">
                @if($activeShift)
                    <div class="text-sm">
                        <span class="text-gray-500">Turno:</span>
                        <span class="font-medium">{{ $activeShift->opened_at->format('H:i') }}</span>
                    </div>
                @endif
                
                <livewire:pos.shift-management-modal />
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex overflow-hidden">
            <!-- Produtos (70%) -->
            <div class="flex-1 border-r">
                <livewire:pos.product-list />
            </div>

            <!-- Carrinho (30%) -->
            <div class="w-96 bg-white flex flex-col">
                <livewire:pos.order-summary 
                    :cart="$cart" 
                    :currentTable="$currentTable"
                />
            </div>
        </div>

        <!-- Tables Modal Component -->
        <livewire:pos.tables-list />

        <!-- Payment Modal -->
        <livewire:pos.payment-modal :cart="$cart" :currentTable="$currentTable" />
    </div>
</div>