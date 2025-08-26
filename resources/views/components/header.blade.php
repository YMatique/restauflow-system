@props([
    'title' => null,
    'subtitle' => null,
    'breadcrumb' => null,
    'actions' => null,
    'variant' => 'primary' // primary, success, warning, danger, purple
])

@php
$gradients = [
    'primary' => 'from-blue-600 to-purple-600',
    'success' => 'from-emerald-500 to-green-600', 
    'warning' => 'from-amber-500 to-orange-600',
    'danger' => 'from-red-500 to-red-600',
    'purple' => 'from-purple-600 to-indigo-600'
];
@endphp

<header class="fixed top-0 left-0 right-0 z-40 bg-gradient-to-r {{ $gradients[$variant] }} text-white shadow-lg">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Left Section -->
            <div class="flex items-center space-x-4">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="text-2xl">🍽️</div>
                    <div class="font-bold text-xl hidden sm:block">RestaurantPOS</div>
                </div>
                
                <!-- Breadcrumb -->
                @if($breadcrumb)
                    <div class="hidden md:flex items-center text-sm text-white/80">
                        <span>{{ $breadcrumb }}</span>
                    </div>
                @endif
            </div>
            
            <!-- Center Section (Title) -->
            @if($title)
                <div class="flex-1 text-center px-4">
                    <h1 class="font-semibold text-lg">{{ $title }}</h1>
                    @if($subtitle)
                        <p class="text-sm text-white/80">{{ $subtitle }}</p>
                    @endif
                </div>
            @endif
            
            <!-- Right Section -->
            <div class="flex items-center space-x-3">
                @if($actions)
                    {{ $actions }}
                @else
                    <!-- Default Actions -->
                    <div class="hidden sm:flex items-center space-x-4 text-sm">
                        <div class="flex items-center space-x-1">
                            <span>👤</span>
                            <span>{{ auth()->user()?->name }}</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <span>🕐</span>
                            <span x-data x-text="new Date().toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' })"></span>
                        </div>
                    </div>
                    
                    <!-- Menu Button (Mobile) -->
                    <button @click="$dispatch('toggle-mobile-menu')" class="sm:hidden p-2 rounded-lg bg-white/10 hover:bg-white/20 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
    </div>
</header>