<!-- Grid de Produtos -->
<div class="grid grid-cols-4 xl:grid-cols-4 lg:grid-cols-3 md:grid-cols-2 sm:grid-cols-2 gap-4">
    @forelse($products as $product)
        <div wire:click="addToCart({{ $product->id }})"
             class="bg-zinc-50 dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 cursor-pointer hover:border-blue-400 hover:shadow-lg transition-all transform hover:scale-105">
            
            <!-- Imagem do Produto -->
            <div class="w-full h-24 bg-zinc-200 dark:bg-zinc-700 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                @if(isset($product->image) && $product->image)
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                @endif
            </div>
            
            <!-- Informações do Produto -->
            <div>
                <h3 class="font-medium text-sm text-zinc-900 dark:text-zinc-100 mb-1 line-clamp-2">
                    {{ $product->name }}
                </h3>
                
                @if(isset($product->description) && $product->description)
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-2 line-clamp-1">
                        {{ $product->description }}
                    </p>
                @endif
                
                <div class="flex items-center justify-between">
                    <p class="text-green-600 dark:text-green-400 font-bold text-sm">
                        {{ number_format($product->price) }} MT
                    </p>
                    
                    <!-- Indicador de Stock -->
                    @if(isset($product->stock_quantity) && $product->stock_quantity <= ($product->min_level ?? 5))
                        <span class="text-xs bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400 px-2 py-0.5 rounded">
                            Baixo
                        </span>
                    @elseif(isset($product->stock_quantity) && $product->stock_quantity > 0)
                        <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400 px-2 py-0.5 rounded">
                            Disponível
                        </span>
                    @else
                        <span class="text-xs bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400 px-2 py-0.5 rounded">
                            Disponível
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">
                📦
            </div>
            <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400 mb-2">
                @if($selectedCategory)
                    Nenhum produto nesta categoria
                @else
                    Nenhum produto disponível
                @endif
            </h3>
            <p class="text-gray-500 dark:text-gray-500">Configure produtos no sistema de gestão</p>
        </div>
    @endforelse
</div>

@if(count($products) > 0)
    <!-- Informações adicionais -->
    <div class="mt-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
        {{ count($products) }} produto(s) disponível(is)
        @if($selectedCategory)
            na categoria selecionada
        @endif
    </div>
@endif