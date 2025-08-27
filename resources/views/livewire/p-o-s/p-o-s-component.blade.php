{{-- resources/views/livewire/p-o-s/p-o-s-component.blade.php --}}
<div class="w-full">
    <div class="flex h-full w-full">
    <!-- Left Sidebar - Categories -->
    <div class="w-48 bg-white border-r-2 border-gray-200 p-4 overflow-y-auto scrollbar-thin">
        <div class="space-y-2">
            <!-- All Categories Button -->
            <button wire:click="selectCategory(null)"
                    class="w-full flex flex-col items-center p-3 rounded-xl transition-all duration-200 
                           {{ $selectedCategory === null ? 'bg-purple-600 text-white' : 'hover:bg-gray-100' }}">
                <div class="text-2xl mb-2">📋</div>
                <div class="text-xs font-medium text-center">Todos</div>
            </button>

            @forelse($categories as $category)
                <button wire:click="selectCategory({{ $category->id }})"
                        class="w-full flex flex-col items-center p-3 rounded-xl transition-all duration-200 
                               {{ $selectedCategory === $category->id ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}">
                    <div class="text-2xl mb-2">{{ $category->emoji ?? '📦' }}</div>
                    <div class="text-xs font-medium text-center">{{ $category->name }}</div>
                </button>
            @empty
                <div class="text-center py-8">
                    <div class="text-3xl mb-2">📋</div>
                    <p class="text-gray-500 text-sm">Nenhuma categoria</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Main Content - Products Grid -->
    <div class="flex-1 p-6 bg-gray-50 overflow-y-auto scrollbar-thin">
        <!-- Products Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    {{ $selectedCategory ? $categories->find($selectedCategory)?->name : 'Todos os Produtos' }}
                </h2>
                <p class="text-sm text-gray-600">{{ count($products) }} produtos disponíveis</p>
            </div>
            
            <!-- Quick Search -->
            <div class="relative">
                <input type="text" 
                       placeholder="🔍 Buscar produto..."
                       class="pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-64">
                <div class="absolute right-3 top-2.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
            @forelse($products as $product)
                 <div wire:click="addToCart({{ $product->id }})"
                     class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer border border-gray-100 overflow-hidden group {{ !$product->canSell() ? 'opacity-50 cursor-not-allowed' : '' }}">
                    
                    <!-- Card Header with Status -->
                    <div class="relative p-4 pb-2">
                        <div class="flex justify-between items-start">
                            <!-- Stock Status -->
                            <div class="flex items-center space-x-1">
                                @if($product->getStockStatus() === 'in_stock')
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                    <span class="text-xs text-green-600 font-medium">Disponível</span>
                                @elseif($product->getStockStatus() === 'low_stock')
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                                    <span class="text-xs text-yellow-600 font-medium">Baixo</span>
                                @else
                                    <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                    <span class="text-xs text-red-600 font-medium">Esgotado</span>
                                @endif
                            </div>
                            
                            <!-- Featured Badge -->
                            @if($product->is_featured)
                                <div class="bg-yellow-100 text-yellow-600 px-2 py-1 rounded-full text-xs font-medium">
                                    ⭐ Destaque
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Image/Icon -->
                    <div class="flex justify-center py-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-3xl group-hover:scale-110 transition-transform duration-300">
                            {{ $product->category->emoji ?? '🍽️' }}
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="px-4 pb-4">
                        <!-- Product Name -->
                        <h3 class="font-semibold text-gray-900 text-sm mb-1 line-clamp-2 leading-tight">
                            {{ $product->name }}
                        </h3>
                        
                        <!-- Product Description -->
                        @if($product->description)
                            <p class="text-xs text-gray-500 mb-2 line-clamp-2">
                                {{ $product->description }}
                            </p>
                        @endif

                        <!-- Price -->
                        <div class="flex items-center justify-between mb-2">
                            <div class="text-lg font-bold text-blue-600">
                                {{ number_format($product->price, 0) }}
                                <span class="text-xs text-gray-500">MT</span>
                            </div>
                            
                            <!-- Stock Quantity -->
                            <div class="text-xs text-gray-500">
                                @if($product->track_stock)
                                    📦 {{ number_format($product->stock_quantity, 0) }}
                                @else
                                    ♾️ Ilimitado
                                @endif
                            </div>
                        </div>

                        <!-- Add Button -->
                        @if($product->canSell())
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200 transform group-hover:scale-105">
                                🛒 Adicionar
                            </button>
                        @else
                            <div class="w-full bg-gray-300 text-gray-500 text-sm font-medium py-2 px-3 rounded-lg text-center">
                                ❌ Indisponível
                            </div>
                        @endif
                    </div>

                    <!-- Unavailable Overlay -->
                    @if(!$product->canSell())
                        <div class="absolute inset-0 bg-gray-900 bg-opacity-20 flex items-center justify-center">
                            <div class="bg-white rounded-full p-3 shadow-lg">
                                <span class="text-2xl">🚫</span>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">🍽️</div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">Nenhum produto encontrado</h3>
                    <p class="text-gray-500">
                        @if($selectedCategory)
                            Não há produtos nesta categoria.
                        @else
                            Configure produtos no sistema de gestão.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Right Sidebar - Cart -->
    @include('livewire.p-o-s.partials.cart-sidebar')
</div>

<!-- Modals -->
@include('livewire.p-o-s.partials.table-modal')
@include('livewire.p-o-s.partials.payment-modal')

<!-- Toast Notifications -->
<!-- Toast Notifications -->
<div x-data="{ 
        show: false, 
        message: '', 
        type: 'success',
        showToast(data) {
            this.message = data.detail[0].message;
            this.type = data.detail[0].type;
            this.show = true;
            setTimeout(() => this.show = false, 3000);
        }
     }" 
     @toast.window="showToast($event)"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     class="fixed top-20 right-4 z-[9999] max-w-sm w-full"
     style="z-index: 9999;">
    
    <div class="border-l-4 p-4 rounded-r-lg shadow-xl backdrop-blur-sm"
         :class="type === 'success' ? 'bg-green-100 border-green-500 text-green-700' : 
                 type === 'error' ? 'bg-red-100 border-red-500 text-red-700' : 
                 type === 'warning' ? 'bg-yellow-100 border-yellow-500 text-yellow-700' : 
                 'bg-blue-100 border-blue-500 text-blue-700'">
        <div class="flex items-center">
            <div class="flex-shrink-0 text-lg">
                <template x-if="type === 'success'">
                    <span>✅</span>
                </template>
                <template x-if="type === 'error'">
                    <span>❌</span>
                </template>
                <template x-if="type === 'warning'">
                    <span>⚠️</span>
                </template>
                <template x-if="type === 'info'">
                    <span>ℹ️</span>
                </template>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium" x-text="message"></p>
            </div>
            <button @click="show = false" class="ml-4 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Keyboard Shortcuts Help -->
<div x-data="{ showHelp: false }" 
     @keydown.window.ctrl.shift.h="showHelp = !showHelp"
     x-show="showHelp"
     x-transition
     class="fixed bottom-4 left-4 bg-gray-900 text-white p-4 rounded-lg shadow-lg z-40">
    
    <h4 class="font-bold mb-2">⌨️ Atalhos de Teclado</h4>
    <div class="text-sm space-y-1">
        <div><kbd class="bg-gray-700 px-2 py-1 rounded text-xs">F1</kbd> Abrir Mesa</div>
        <div><kbd class="bg-gray-700 px-2 py-1 rounded text-xs">F2</kbd> Finalizar Pagamento</div>
        <div><kbd class="bg-gray-700 px-2 py-1 rounded text-xs">F3</kbd> Limpar Carrinho</div>
        <div><kbd class="bg-gray-700 px-2 py-1 rounded text-xs">Esc</kbd> Fechar Modais</div>
        <div><kbd class="bg-gray-700 px-2 py-1 rounded text-xs">Ctrl+Shift+H</kbd> Esta ajuda</div>
    </div>
</div>

</div>

@section('styles')
<style>
    .scrollbar-thin::-webkit-scrollbar {
        width: 4px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
/* Line clamp for text truncation */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection



@section('scripts')
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ignore if typing in input field
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }
        
        switch(e.key) {
            case 'F1':
                e.preventDefault();
                @this.openTableModal();
                break;
            case 'F2':
                e.preventDefault();
                if (@this.cart && Object.keys(@this.cart).length > 0) {
                    @this.openPaymentModal();
                }
                break;
            case 'F3':
                e.preventDefault();
                if (confirm('Limpar carrinho?')) {
                    @this.clearCart();
                }
                break;
            case 'Escape':
                e.preventDefault();
                @this.closeTableModal();
                @this.closePaymentModal();
                break;
        }
    });
    
    // Auto-refresh shift info every minute
    setInterval(function() {
        @this.call('$refresh');
    }, 60000);
});
</script>
@endsection