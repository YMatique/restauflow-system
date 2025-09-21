<div class="p-4">
    <h3 class="text-lg font-semibold mb-4">Selecionar Mesa</h3>
    
    @foreach($tablesBySection as $section => $tables)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-600 mb-2">{{ $section ?: 'Sem Secção' }}</h4>
            
            <div class="grid grid-cols-3 gap-2">
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