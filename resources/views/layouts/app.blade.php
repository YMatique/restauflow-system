<!DOCTYPE html>
<html lang="pt" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'RestaurantPOS' }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <!-- Alpine.js -->
    {{-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom Variables */
        :root {
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #10b981, #059669);
            --gradient-warning: linear-gradient(135deg, #f59e0b, #d97706);
            --gradient-danger: linear-gradient(135deg, #ef4444, #dc2626);
            --gradient-purple: linear-gradient(135deg, #7c3aed, #6d28d9);
        }
        
        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 antialiased">
    <!-- Loading Screen -->
    <div id="loading-screen" class="fixed inset-0 bg-white z-50 flex items-center justify-center" x-show="$store.loading" x-cloak>
        <div class="text-center">
            <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-gray-600">Carregando...</p>
        </div>
    </div>

    <!-- Header Component -->
    @if(!isset($hideHeader))
        @include('layouts.components.header')
    @endif
    
    <!-- Main Content -->
    <main class="{{ isset($fullScreen) ? '' : 'pt-16' }}">
        {{ $slot }}
    </main>
    
    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-20 right-4 z-50 space-y-2">
        <!-- Toasts will be inserted here -->
    </div>
    
    <!-- Global Alpine Store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('loading', false);
            Alpine.store('user', @json(auth()->user()));
            Alpine.store('company', @json(auth()->user()?->company));
        });
    </script>
    
    @livewireScripts
    @stack('scripts')
</body>
</html>