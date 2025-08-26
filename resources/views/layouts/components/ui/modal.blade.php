{{-- components/ui/modal.blade.php --}}
@props([
    'show' => false,
    'maxWidth' => 'md', // sm, md, lg, xl, full
    'title' => null,
    'closable' => true
])

@php
$widths = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg', 
    'xl' => 'sm:max-w-xl',
    'full' => 'sm:max-w-full sm:mx-4'
];
@endphp

<div x-data="{ show: @js($show) }" 
     x-show="show"
     x-on:close-modal.window="show = false"
     x-on:open-modal.window="show = true"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
     
    <!-- Backdrop -->
    <div x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-50"></div>

    <!-- Modal -->
    <div x-show="show" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="flex items-center justify-center min-h-screen p-4">
         
        <div class="bg-white rounded-xl shadow-2xl w-full {{ $widths[$maxWidth] }} max-h-[90vh] overflow-hidden">
            @if($title || $closable)
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    @if($title)
                        <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
                    @endif
                    
                    @if($closable)
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    @endif
                </div>
            @endif
            
            <div class="overflow-y-auto max-h-[calc(90vh-80px)]">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
