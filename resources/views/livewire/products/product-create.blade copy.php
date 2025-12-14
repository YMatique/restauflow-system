<div class="space-y-6">

    <!-- Header -->
    <div class="flex justify-between items-center py-6 bg-white p-3">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
            Criar Produto
        </h1>

        <a href="{{ route('restaurant.products') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white font-medium rounded-lg shadow transition-all duration-200 transform hover:scale-105">
            Voltar
        </a>
    </div>

    <!-- Form -->
    <form wire:submit.prevent="save"
          class="bg-white dark:bg-zinc-800 p-6 rounded-xl shadow space-y-6">

        <!-- Código e Nome -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Código *</label>
                <input wire:model.defer="form.code"
                       class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                @error('form.code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Nome *</label>
                <input wire:model.defer="form.name"
                       class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                @error('form.name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Description e Category -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Descrição</label>
                <input wire:model.defer="form.description"
                       class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                @error('form.description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Categoria *</label>
                <select wire:model.defer="form.category_id"
                        class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">Selecione</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Subcategory e Cost -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Subcategoria *</label>
                <select wire:model.defer="form.subcategory_id"
                        class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">Selecione</option>
                    @foreach($subcategories as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Custo</label>
                <input type="number" step="0.01" wire:model.defer="form.cost"
                       class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>
        </div>

        <!-- Price e Type -->
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Preço *</label>
                <input type="number" step="0.01" wire:model.defer="form.price"
                       class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
            </div>

            <div>
                <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Tipo</label>
                <select wire:model.live="form.type"
                        class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="simple">Simples</option>
                    <option value="composed">Composto</option>
                </select>
            </div>
        </div>

        <!-- Stock (only simple) -->
        @if($form['type'] === 'simple')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Quantidade</label>
                    <input wire:model.defer="form.stock_quantity"
                           class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>
                <div>
                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Stock mínimo</label>
                    <input wire:model.defer="form.min_level"
                           class="w-full px-3 py-2 bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-600 rounded-lg text-zinc-900 dark:text-zinc-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>
            </div>
        @endif

        <!-- Flags -->
        <div class="flex gap-6">
            <label class="flex items-center gap-2 text-zinc-700 dark:text-zinc-300">
                <input type="checkbox" wire:model="form.track_stock" class="accent-blue-500"> Controlar stock
            </label>
            <label class="flex items-center gap-2 text-zinc-700 dark:text-zinc-300">
                <input type="checkbox" wire:model="form.is_active" class="accent-blue-500"> Ativo
            </label>
            <label class="flex items-center gap-2 text-zinc-700 dark:text-zinc-300">
                <input type="checkbox" wire:model="form.is_featured" class="accent-blue-500"> Destaque
            </label>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('restaurant.products') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-zinc-200 hover:bg-zinc-300 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-zinc-900 dark:text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                Salvar Produto
            </button>
        </div>

    </form>
</div>
