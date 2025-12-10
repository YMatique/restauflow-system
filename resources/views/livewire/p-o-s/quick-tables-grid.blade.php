<!-- Acesso Rápido às Mesas -->
<div class="grid grid-cols-4 gap-2">
    @foreach($quickTables as $table)
        <button wire:click="selectTable({{ $table->id }})"
                @class([
                    'w-12 h-12 rounded-lg text-sm font-bold transition-all duration-200 transform hover:scale-105 text-white',
                    'bg-green-500 hover:bg-green-600' => $table->status === 'available',
                    'bg-red-500 hover:bg-red-600' => $table->status === 'occupied',
                    'bg-yellow-500 hover:bg-yellow-600' => $table->status === 'reserved',
                    'bg-gray-500 hover:bg-gray-600' => $table->status === 'maintenance'
                ])>
            {{ $table->name }}
        </button>
    @endforeach
    
    @for($i = count($quickTables); $i < 8; $i++)
        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center text-gray-400">
            -
        </div>
    @endfor
</div>