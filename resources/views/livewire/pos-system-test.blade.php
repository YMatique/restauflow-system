<div>
    {{-- Sidebar Navigation --}}
    @slot('sidebarNavigation')
        <button wire:click="$set('selectedCategory', 'all')" 
                class="w-full text-left p-3 rounded-lg {{ $selectedCategory === 'all' ? 'bg-blue-500 text-white' : 'hover:bg-gray-100' }} transition-all">
            📋 Todos os Produtos
        </button>
        <button wire:click="$set('selectedCategory', 'bebidas')" 
                class="w-full text-left p-3 rounded-lg {{ $selectedCategory === 'bebidas' ? 'bg-blue-500 text-white' : 'hover:bg-gray-100' }} transition-all">
            🥤 Bebidas
        </button>
        <button wire:click="$set('selectedCategory', 'pratos')" 
                class="w-full text-left p-3 rounded-lg {{ $selectedCategory === 'pratos' ? 'bg-blue-500 text-white' : 'hover:bg-gray-100' }} transition-all">
            🍽️ Pratos Principais
        </button>
    @endslot

    {{-- Quick Actions --}}
    @slot('quickActions')
        <a href="" class="w-full text-left p-2 text-sm rounded hover:bg-gray-100 transition-colors block">
            📦 Gestão Stock
        </a>
        <a href="" class="w-full text-left p-2 text-sm rounded hover:bg-gray-100 transition-colors block">
            📊 Relatórios
        </a>
    @endslot

    {{-- Sidebar Footer --}}
    @slot('sidebarFooter')
        <h4 class="font-semibold text-gray-900 mb-3">Resumo do Dia</h4>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Vendas:</span>
                <span class="font-medium text-green-600">€347.50</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Pedidos:</span>
                <span class="font-medium">23</span>
            </div>
        </div>
    @endslot

    {{-- Right Panel - Cart --}}
    @slot('rightPanel')
        <div class="flex flex-col h-full">
            <div class="p-4 border-b bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-900">🛒 Carrinho</h3>
                    <span class="bg-blue-500 text-white text-sm px-2 py-1 rounded-full">{{ count($cart) }}</span>
                </div>
            </div>
            
            <div class="flex-1 overflow-auto p-4">
                @forelse($cart as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2">
                        <div class="flex-1">
                            <h4 class="font-medium">{{ $item['name'] }}</h4>
                            <p class="text-sm text-gray-600">€{{ number_format($item['price'], 2) }} x {{ $item['quantity'] }}</p>
                        </div>
                        <button wire:click="removeFromCart({{ $item['id'] }})" class="text-red-500 hover:text-red-700">
                            🗑️
                        </button>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <div class="text-4xl mb-2">🛒</div>
                        <p>Carrinho vazio</p>
                    </div>
                @endforelse
            </div>
            
            <div class="border-t p-4 bg-gray-50">
                <button wire:click="processSale" 
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded-lg disabled:opacity-50"
                        {{ empty($cart) ? 'disabled' : '' }}>
                    💰 Finalizar Venda
                </button>
            </div>
        </div>
    @endslot

    {{-- Main Content - Products --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        {{-- @foreach($this->getProducts() as $product)
            <div wire:click="addToCart({{ $product->id }})" 
                 class="bg-white rounded-xl shadow-md p-4 cursor-pointer hover:shadow-lg transition-all transform hover:scale-[1.02] {{ $product->isAvailable() ? '' : 'opacity-60' }}">
                <div class="text-center">
                    <div class="text-5xl mb-3">{{ $product->emoji }}</div>
                    <h3 class="font-semibold mb-1">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500 mb-2">Stock: {{ $product->availableQuantity() }}</p>
                    <p class="text-xl font-bold text-blue-600 mb-3">€{{ number_format($product->price, 2) }}</p>
                    <span class="inline-block {{ $product->isAvailable() ? 'bg-green-500' : 'bg-red-500' }} text-white px-3 py-1 rounded-full text-xs font-medium">
                        {{ $product->isAvailable() ? '✓ Disponível' : '✗ Indisponível' }}
                    </span>
                </div>
            </div>
        @endforeach --}}
    </div>
</div>
