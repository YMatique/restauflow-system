<div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Titulo da Página</h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Subtítulo ou descrição da página
            </p>
        </div>

        <button
            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Botão (opcional)
        </button>
    </div>
    {{-- The best athlete wants his opponent at his best. --}}

    <button wire:click="testFunction()" class="bg-green-600 text-white px-4 py-2 rounded-lg"> Teste de toast</button>
</div>
