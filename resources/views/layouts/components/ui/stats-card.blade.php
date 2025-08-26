{{-- components/ui/stats-card.blade.php --}}
@props([
    'title',
    'value', 
    'subtitle' => null,
    'icon' => null,
    'color' => 'blue', // blue, green, yellow, red, purple
    'trend' => null // 'up', 'down', 'neutral'
])

@php
$colors = [
    'blue' => 'from-blue-500 to-blue-600',
    'green' => 'from-emerald-500 to-green-600',
    'yellow' => 'from-amber-500 to-orange-600', 
    'red' => 'from-red-500 to-red-600',
    'purple' => 'from-purple-500 to-indigo-600'
];

$trendIcons = [
    'up' => '📈',
    'down' => '📉',
    'neutral' => '➡️'
];
@endphp

<div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-1 overflow-hidden">
    <div class="h-1 bg-gradient-to-r {{ $colors[$color] }}"></div>
    
    <div class="p-6">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600 uppercase tracking-wider">{{ $title }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ $value }}</p>
                
                @if($subtitle)
                    <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
                @endif
                
                @if($trend)
                    <div class="flex items-center mt-2 text-sm">
                        <span class="mr-1">{{ $trendIcons[$trend] }}</span>
                        <span class="{{ $trend === 'up' ? 'text-green-600' : ($trend === 'down' ? 'text-red-600' : 'text-gray-600') }}">
                            {{ $slot }}
                        </span>
                    </div>
                @endif
            </div>
            
            @if($icon)
                <div class="bg-gradient-to-r {{ $colors[$color] }} p-3 rounded-xl">
                    <span class="text-2xl">{{ $icon }}</span>
                </div>
            @endif
        </div>
    </div>
</div>