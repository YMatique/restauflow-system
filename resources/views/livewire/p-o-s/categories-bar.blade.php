<!-- Barra de Categorias -->
<div class="flex gap-2 mb-6 overflow-x-auto pb-2">
    <button wire:click="selectCategory(null)"
            @class([
                'px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors',
                'bg-blue-600 text-white' => is_null($selectedCategory),
                'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600' => !is_null($selectedCategory)
            ])>
        Todas
    </button>

    @foreach($categories as $category)
        <button wire:click="selectCategory({{ $category->id }})"
                @class([
                    'px-4 py-2 rounded-lg text-sm font-medium whitespace-nowrap transition-colors',
                    'bg-blue-600 text-white' => $selectedCategory == $category->id,
                    'bg-zinc-200 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-300 dark:hover:bg-zinc-600' => $selectedCategory != $category->id
                ])>
            {{ $category->name }}
        </button>
    @endforeach
</div>