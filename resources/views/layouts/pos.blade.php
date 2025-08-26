<!DOCTYPE html>
<html lang="pt" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'POS - RestaurantPOS' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        body { overflow: hidden; }
    </style>
    
    @stack('styles')
</head>
<body class="h-screen bg-gray-50 antialiased">
    <!-- POS Header -->
    <header class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-3 flex justify-between items-center shadow-lg">
        <div class="flex items-center space-x-4">
            <div class="text-2xl">🍽️</div>
            <div class="font-bold text-xl">RestaurantPOS</div>
            
            <!-- Current Table/Order Info -->
            @if(isset($currentTable))
                <div class="bg-white/20 px-3 py-1 rounded-full text-sm font-medium cursor-pointer hover:bg-white/30 transition-colors"
                     @click="$dispatch('select-table')">
                    🍽 {{ $currentTable }}
                </div>
            @endif
        </div>
        
        <div class="flex items-center space-x-6 text-sm">
            <div class="flex items-center space-x-1">
                <span>👤</span>
                <span>{{ auth()->user()?->name }}</span>
            </div>
            <div class="flex items-center space-x-1">
                <span>💰</span>
                <span>Turno: {{ $shiftInfo ?? '08:00 - 20:00' }}</span>
            </div>
            <div class="flex items-center space-x-1">
                <span>🕐</span>
                <span x-data x-text="new Date().toLocaleTimeString('pt-PT', { hour: '2-digit', minute: '2-digit' })"></span>
            </div>
        </div>
    </header>

    <!-- Main POS Content -->
    <main class="flex h-[calc(100vh-64px)]">
        {{ $slot }}
    </main>
    
    <!-- Modals Container -->
    <div id="modals-container"></div>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>