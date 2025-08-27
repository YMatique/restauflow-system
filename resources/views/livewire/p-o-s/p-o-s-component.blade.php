{{-- resources/views/livewire/p-o-s/p-o-s-component.blade.php --}}
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
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($products as $product)
                <div wire:click="addToCart({{ $product->id }})"
                     class="product-card {{ !$product->canSell() ? 'unavailable' : '' }}"
                     title="{{ $product->description }}">
                    
                    <!-- Stock Indicator -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="stock-indicator {{ $product->getStockStatus() }}"></div>
                        @if($product->is_featured)
                            <div class="text-yellow-500 text-lg">⭐</div>
                        @endif
                    </div>
                    
                    <!-- Product Image/Icon -->
                    <div class="product-image">
                        {{ $product->category->emoji ?? '🍽️' }}
                    </div>
                    
                    <!-- Product Info -->
                    <div class="product-info">
                        <h3 class="product-name">{{ $product->name }}</h3>
                        
                        <div class="product-price">
                            {{ number_format($product->price, 0) }} MT
                        </div>
                        
                        <!-- Stock Info -->
                        <div class="stock-info">
                            @if($product->track_stock)
                                Stock: {{ number_format($product->stock_quantity, 0) }}
                            @else
                                <span class="text-green-600">✓ Disponível</span>
                            @endif
                        </div>
                        
                        @if(!$product->canSell())
                            <div class="unavailable-badge">
                                ❌ Indisponível
                            </div>
                        @endif
                    </div>
                    
                    <!-- Quick Add Button -->
                    @if($product->canSell())
                    <div class="quick-add-btn">
                        <button class="add-btn">
                            ➕ Adicionar
                        </button>
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

<!-- Styles -->
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

    .product-card {
        @apply bg-white rounded-xl p-4 cursor-pointer transition-all duration-200 hover:shadow-lg hover:-translate-y-1 relative overflow-hidden;
        min-height: 160px;
    }
    
    .product-card.unavailable {
        @apply opacity-50 cursor-not-allowed;
    }
    
    .product-card.unavailable:hover {
        transform: none;
        box-shadow: none;
    }
    
    .stock-indicator {
        @apply w-3 h-3 rounded-full;
    }
    
    .stock-indicator.in_stock {
        @apply bg-green-500;
    }
    
    .stock-indicator.low_stock {
        @apply bg-yellow-500;
    }
    
    .stock-indicator.out_of_stock {
        @apply bg-red-500;
    }
    
    .product-image {
        @apply bg-gray-100 rounded-lg h-16 flex items-center justify-center text-3xl mb-3;
    }
    
    .product-info {
        @apply text-center flex-1;
    }
    
    .product-name {
        @apply font-semibold text-gray-800 text-sm mb-1 leading-tight;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .product-price {
        @apply text-lg font-bold text-blue-600 mb-1;
    }
    
    .stock-info {
        @apply text-xs text-gray-500;
    }
    
    .unavailable-badge {
        @apply text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded-full mt-2;
    }
    
    .quick-add-btn {
        @apply absolute bottom-0 left-0 right-0 bg-gradient-to-t from-white via-white to-transparent pt-4 pb-2 px-2 opacity-0 transition-opacity;
    }
    
    .product-card:hover .quick-add-btn {
        @apply opacity-100;
    }
    
    .add-btn {
        @apply w-full py-2 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors;
    }
</style>

<!-- Toast Notifications -->
<div x-data="{ 
    show: false, 
    message: '', 
    type: 'success' 
}" 
     @toast.window="
        message = $event.detail.message; 
        type = $event.detail.type; 
        show = true; 
        setTimeout(() => show = false, 3000)
     "
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform translate-y-2"
     class="fixed top-4 right-4 z-50 max-w-sm w-full">
    
    <div :class="{
        'bg-green-100 border-green-500 text-green-700': type === 'success',
        'bg-red-100 border-red-500 text-red-700': type === 'error',
        'bg-yellow-100 border-yellow-500 text-yellow-700': type === 'warning',
        'bg-blue-100 border-blue-500 text-blue-700': type === 'info'
    }" 
    class="border-l-4 p-4 rounded-r-lg shadow-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <span x-show="type === 'success'">✅</span>
                <span x-show="type === 'error'">❌</span>
                <span x-show="type === 'warning'">⚠️</span>
                <span x-show="type === 'info'">ℹ️</span>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium" x-text="message"></p>
            </div>
            <button @click="show = false" class="ml-4 text-gray-400 hover:text-gray-600">
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