<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center py-6 bg-white p-3">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
            Visualizar Produto
        </h1>

        <a href="{{ route('restaurant.products') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white font-medium rounded-lg shadow transition-all duration-200 transform hover:scale-105">
            Voltar
        </a>
    </div>

    <!-- Product Details -->
    <div class="bg-white dark:bg-zinc-800 p-6 rounded-xl shadow space-y-6">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Código</label>
                <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $product->code }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nome</label>
                <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $product->name }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descrição</label>
                <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $product->description ?? '-' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Categoria</label>
                <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $product->category->name ?? '-' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Subcategoria</label>
                <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $product->subcategory->name ?? '-' }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo</label>
                <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ ucfirst($product->type) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Preço</label>
                <p class="mt-1 text-zinc-900 dark:text-zinc-100">MZN {{ number_format($product->price, 2, ',', '.') }}</p>
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Custo</label>
                <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ $product->cost ? 'R$ '.number_format($product->cost, 2, ',', '.') : '-' }}</p>
            </div>
        </div>

        @if($product->type === 'simple')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Quantidade</label>
                    <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{ (int)$product->stock_quantity }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Stock mínimo</label>
                    <p class="mt-1 text-zinc-900 dark:text-zinc-100">{{(int) $product->min_level }}</p>
                </div>
            </div>
        @endif

        <!-- Flags -->
        <div class="flex gap-6">
            <p class="text-zinc-900 dark:text-zinc-100"><strong>Controlar stock:</strong> {{ $product->track_stock ? 'Sim' : 'Não' }}</p>
            <p class="text-zinc-900 dark:text-zinc-100"><strong>Ativo:</strong> {{ $product->is_active ? 'Sim' : 'Não' }}</p>
            <p class="text-zinc-900 dark:text-zinc-100"><strong>Destaque:</strong> {{ $product->is_featured ? 'Sim' : 'Não' }}</p>
        </div>

    </div>
</div>
