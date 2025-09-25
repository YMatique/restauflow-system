<!-- Grid Principal de Mesas -->
<div class="grid grid-cols-6 xl:grid-cols-6 lg:grid-cols-5 md:grid-cols-4 sm:grid-cols-3 gap-4">
    @forelse($tables as $table)
        <div wire:click="selectTable({{ $table->id }})"
             @class([
                 'w-20 h-20 rounded-xl flex items-center justify-center font-bold cursor-pointer transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl text-white',
                 'bg-green-500 hover:bg-green-600' => $table->status === 'available',
                 'bg-red-500 hover:bg-red-600' => $table->status === 'occupied', 
                 'bg-yellow-500 hover:bg-yellow-600' => $table->status === 'reserved',
                 'bg-gray-500 hover:bg-gray-600' => $table->status === 'maintenance'
             ])>
            <div class="text-center">
                <div class="text-lg font-bold">{{ $table->name }}</div>
                @if($table->capacity)
                    <div class="text-xs opacity-80">{{ $table->capacity }}p</div>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                🍽️
            </div>
            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400 mb-2">Nenhuma mesa configurada</h3>
            <p class="text-gray-500 dark:text-gray-500">Configure as mesas no sistema de gestão</p>
        </div>
    @endforelse
</div>