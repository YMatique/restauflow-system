<div>
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div wire:click="closeModal" class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full p-6 max-h-[80vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold">Selecionar Mesa</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    @foreach($tablesBySection as $section => $tables)
                        <div>
                            <h4 class="text-sm font-medium text-gray-600 mb-3">{{ $section ?: 'Sem Secção' }}</h4>
                            
                            <div class="grid grid-cols-4 gap-3">
                                @foreach($tables as $table)
                                    <button 
                                        wire:click="selectTable({{ $table->id }})"
                                        @class([
                                            'p-4 rounded-lg border-2 transition-all',
                                            'border-green-500 bg-green-50' => $table->isAvailable() && $currentTableId === $table->id,
                                            'border-gray-300 hover:border-green-400' => $table->isAvailable() && $currentTableId !== $table->id,
                                            'border-red-300 bg-red-50 opacity-50 cursor-not-allowed' => !$table->isAvailable()
                                        ])
                                        @disabled(!$table->isAvailable())
                                    >
                                        <div class="text-center">
                                            <div class="font-semibold">{{ $table->name }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $table->capacity }} pessoas
                                            </div>
                                            <div class="text-xs mt-1">
                                                <span @class([
                                                    'px-2 py-1 rounded-full',
                                                    'bg-green-100 text-green-800' => $table->isAvailable(),
                                                    'bg-red-100 text-red-800' => !$table->isAvailable()
                                                ])>
                                                    {{ $table->isAvailable() ? 'Disponível' : 'Ocupada' }}
                                                </span>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>