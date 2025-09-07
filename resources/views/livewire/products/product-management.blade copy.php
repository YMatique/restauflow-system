{{-- resources/views/livewire/products/product-management.blade.php --}}
<div>
    <x-layouts.header 
        title="Gestão de Produtos" 
        breadcrumb="Dashboard > Produtos"
        variant="purple"
    >
        <x-slot:actions>
            <div class="flex items-center space-x-3">
                <div class="relative">
                    <input 
                        wire:model.live.debounce.300ms="search"
                        type="text" 
                        placeholder="Buscar produtos..."
                        class="pl-10 pr-4 py-2 w-80 border border-white/30 rounded-lg bg-white/20 text-white placeholder-white/70 focus:outline-none focus:ring-2 focus:ring-white/50"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <x-ui.button 
                    wire:click="$set('showModal', true)"
                    variant="secondary"
                    icon="➕"
                >
                    Novo Produto
                </x-ui.button>
            </div>
        </x-slot:actions>
    </x-layouts.header>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filters -->
        <x-ui.card class="mb-8">
            <div class="flex flex-wrap gap-4 items-center">
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Categoria</label>
                    <select wire:model.live="categoryFilter" class="border border-gray-300 rounded-lg px-3 py-2 min-w-[140px]">
                        <option value="">Todas</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->emoji }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 min-w-[140px]">
                        <option value="">Todos</option>
                        <option value="available">Disponível</option>
                        <option value="unavailable">Indisponível</option>
                        <option value="low-stock">Stock Baixo</option>
                    </select>
                </div>
                
                <div class="flex flex-col">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Visualização</label>
                    <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                        <button 
                            wire:click="$set('viewMode', 'grid')"
                            class="px-3 py-2 {{ $viewMode === 'grid' ? 'bg-purple-600 text-white' : 'bg-white hover:bg-gray-50' }}"
                        >
                            ⊞
                        </button>
                        <button 
                            wire:click="$set('viewMode', 'table')"
                            class="px-3 py-2 {{ $viewMode === 'table' ? 'bg-purple-600 text-white' : 'bg-white hover:bg-gray-50' }}"
                        >
                            ☰
                        </button>
                    </div>
                </div>
            </div>
        </x-ui.card>
        
        <!-- Products Display -->
        @if($viewMode === 'grid')
            <!-- Grid View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($products as $product)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-1 overflow-hidden">
                        <!-- Product Image -->
                        <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-5xl relative">
                            {{ $product->category->emoji ?? '🍽️' }}
                            
                            <!-- Status Badge -->
                            <div class="absolute top-3 right-3">
                                @if($product->is_available && $product->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Disponível</span>
                                @elseif($product->getStockStatus() === 'low_stock')
                                    <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Stock Baixo</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Indisponível</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                                <span class="px-2 py-1 text-xs font-medium rounded {{ $product->type === 'simple' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $product->type === 'simple' ? 'Simples' : 'Composto' }}
                                </span>
                            </div>
                            
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                            
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xl font-bold text-green-600">{{ number_format($product->price, 0) }} MT</span>
                                <span class="text-sm text-gray-500">
                                    Stock: {{ $product->track_stock ? $product->stock_quantity . ' unid.' : '∞' }}
                                </span>
                            </div>
                            
                            <div class="flex space-x-2">
                                <x-ui.button 
                                    wire:click="editProduct({{ $product->id }})"
                                    variant="secondary"
                                    size="sm"
                                    class="flex-1"
                                    icon="✏️"
                                >
                                    Editar
                                </x-ui.button>
                                <x-ui.button 
                                    wire:click="deleteProduct({{ $product->id }})"
                                    variant="danger"
                                    size="sm"
                                    class="flex-1"
                                    icon="🗑️"
                                >
                                    Remover
                                </x-ui.button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum produto encontrado</h3>
                        <p class="text-gray-600">Tente ajustar os filtros ou adicionar novos produtos</p>
                    </div>
                @endforelse
            </div>
        @else
            <!-- Table View -->
            <x-ui.card padding="none">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-2xl mr-3">{{ $product->category->emoji ?? '🍽️' }}</div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-500">{{ Str::limit($product->description, 50) }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->category->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-600">
                                        {{ number_format($product->price, 0) }} MT
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->track_stock ? $product->stock_quantity . ' unid.' : '∞' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($product->is_available && $product->is_active)
                                            <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Disponível</span>
                                        @elseif($product->getStockStatus() === 'low_stock')
                                            <span class="px-2 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">Stock Baixo</span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">Indisponível</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <x-ui.button 
                                                wire:click="editProduct({{ $product->id }})"
                                                variant="secondary"
                                                size="sm"
                                            >
                                                ✏️
                                            </x-ui.button>
                                            <x-ui.button 
                                                wire:click="deleteProduct({{ $product->id }})"
                                                variant="danger"
                                                size="sm"
                                            >
                                                🗑️
                                            </x-ui.button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="text-4xl mb-4">🔍</div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Nenhum produto encontrado</h3>
                                        <p class="text-gray-600">Tente ajustar os filtros ou adicionar novos produtos</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $products->links() }}
                </div>
            </x-ui.card>
        @endif
    </div>
    
    <!-- Product Modal -->
    <x-ui.modal :show="$showModal" :title="$editingProduct ? 'Editar Produto' : 'Novo Produto'" maxWidth="lg">
        <form wire:submit.prevent="saveProduct" class="p-6 space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nome do Produto *</label>
                    <input 
                        wire:model="productForm.name"
                        type="text" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        required
                    >
                    @error('productForm.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoria *</label>
                    <select 
                        wire:model="productForm.category_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        required
                    >
                        <option value="">Selecionar categoria</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->emoji }} {{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('productForm.category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                <textarea 
                    wire:model="productForm.description"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    rows="3"
                    placeholder="Descreva o produto..."
                ></textarea>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preço (MT) *</label>
                    <input 
                        wire:model="productForm.price"
                        type="number" 
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        required
                    >
                    @error('productForm.price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo *</label>
                    <select 
                        wire:model="productForm.type"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                        required
                    >
                        <option value="simple">Produto Simples</option>
                        <option value="composed">Produto Composto</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock Atual</label>
                    <input 
                        wire:model="productForm.stock_quantity"
                        type="number" 
                        step="0.1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    >
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stock Mínimo</label>
                    <input 
                        wire:model="productForm.min_level"
                        type="number" 
                        step="0.1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    >
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Código de Barras</label>
                <input 
                    wire:model="productForm.barcode"
                    type="text" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                    placeholder="Opcional"
                >
            </div>
            
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700">Configurações</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input wire:model="productForm.track_stock" type="checkbox" class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Controlar stock</span>
                    </label>
                    <label class="flex items-center">
                        <input wire:model="productForm.is_available" type="checkbox" class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Produto disponível</span>
                    </label>
                    <label class="flex items-center">
                        <input wire:model="productForm.is_active" type="checkbox" class="rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Produto ativo</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6">
                <x-ui.button 
                    type="button"
                    wire:click="resetForm"
                    variant="secondary"
                >
                    Cancelar
                </x-ui.button>
                <x-ui.button 
                    type="submit"
                    variant="primary"
                    :loading="$loading"
                >
                    {{ $editingProduct ? 'Atualizar' : 'Criar' }} Produto
                </x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>