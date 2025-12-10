<div class="h-full flex flex-col">
    <!-- Categorias -->
    <div class="p-4 border-b overflow-x-auto">
        <div class="flex gap-2">
            @foreach($categories as $category)
                <button 
                    wire:click="selectCategory({{ $category->id }})"
                    @class([
                        'px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors',
                        'bg-blue-500 text-white' => $selectedCategory === $category->id,
                        'bg-gray-100 text-gray-700 hover:bg-gray-200' => $selectedCategory !== $category->id
                    ])
                >
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Busca -->
    <div class="p-4 border-b">
        <input 
            type="text" 
            wire:model.live.debounce.300ms="searchTerm"
            placeholder="Buscar produtos..."
            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
    </div>

    <!-- Lista de Produtos -->
    <div class="flex-1 overflow-y-auto p-4">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($products as $product)
                <button 
                    wire:click="addToCart({{ $product->id }})"
                    class="p-4 bg-white rounded-lg shadow hover:shadow-lg transition-shadow border border-gray-200"
                >
                    @if($product->image_url)
                        <img 
                            src="{{ $product->image_url }}" 
                            alt="{{ $product->name }}"
                            class="w-full h-32 object-cover rounded-lg mb-2"
                        >
                    @endif
                    
                    <h4 class="font-semibold text-sm mb-1">{{ $product->name }}</h4>
                    
                    <div class="text-lg font-bold text-blue-600">
                        {{ number_format($product->price, 2) }} MT
                    </div>
                    
                    <div class="text-xs text-gray-500 mt-1">
                        Stock: {{ $product->stock_quantity }}
                    </div>
                </button>
            @empty
                <div class="col-span-full text-center py-8 text-gray-500">
                    Nenhum produto encontrado
                </div>
            @endforelse
        </div>
    </div>
</div>