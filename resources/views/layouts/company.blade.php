<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ $title ?? 'RestauFlow' }} - {{ tenant()->name ?? 'RestauFlow' }}</title> --}}
    <title>Restaurant </title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Additional Styles -->
    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased" x-data="{ sidebarOpen: false }">
    <!-- Top Header -->
    <header class="fixed top-0 left-0 right-0 z-40 bg-white shadow-sm border-b-2 border-blue-500">
        <div class="flex items-center justify-between p-4">
            <!-- Logo & Brand -->
            <div class="flex items-center space-x-3">
                <!-- Mobile Menu Button -->
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8 12L8 20M6 12L6 16M10 12L10 16" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            <path d="M12 8C14 6 18 6 20 8" stroke="white" stroke-width="1.5" fill="none"/>
                            <path d="M10 12C12 10 16 10 18 12" stroke="white" stroke-width="1.5" fill="none"/>
                            <path d="M12 16C14 14 18 14 20 16" stroke="white" stroke-width="1.5" fill="none"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">RestauFlow</h1>
                        <p class="text-xs text-gray-500 hidden sm:block">{{ $subtitle ?? 'Terminal POS' }}</p>
                    </div>
                </div>
            </div>

            <!-- Tenant & Context Info -->
            <div class="text-center hidden md:block">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{-- {{ tenant()->data['emoji'] ?? '🍽️' }} {{ tenant()->name }} --}}
                    Restaurante
                </h2>
                <p class="text-sm text-gray-500">
                    @if(isset($context))
                        {{ $context }}
                    @else
                    Terminal
                        {{-- Terminal {{ auth()->user()->name ?? 'POS' }} --}}
                    @endif
                </p>
            </div>

            <!-- User & Actions -->
            <div class="flex items-center space-x-2 md:space-x-4">
                <!-- Stock Alerts (if applicable) -->
                @if(isset($showStockAlerts) && $showStockAlerts)
                    {{-- <livewire:stock-alerts-button /> --}}
                @endif

                <!-- Notifications -->
                @if(isset($showNotifications) && $showNotifications)
                    <button class="relative p-2 text-gray-600 hover:text-gray-900 rounded-lg hover:bg-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM12 2a10 10 0 100 20 10 10 0 000-20z"></path>
                        </svg>
                        @if(isset($notificationCount) && $notificationCount > 0)
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                {{ $notificationCount }}
                            </span>
                        @endif
                    </button>
                @endif

                <!-- User Info -->
                <div class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-2">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>
                
                <!-- Settings/Menu -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="bg-gray-200 hover:bg-gray-300 p-2 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zM12 13a1 1 0 110-2 1 1 0 010 2zM12 20a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            📊 Dashboard
                        </a>
                        <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            📦 Gestão Stock
                        </a>
                        <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            📈 Relatórios
                        </a>
                        <hr class="my-1">
                        <a href="" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            ⚙️ Configurações
                        </a>
                        <form method="POST" action="">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                🚪 Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex pt-20">
        <!-- Sidebar -->
        @if(isset($showSidebar) && $showSidebar)
            <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-lg border-r transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0"
                   :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }"
                   style="top: 80px;">
                
                <!-- Sidebar Content -->
                <div class="flex flex-col h-full">
                    <!-- Navigation Section -->
                    @if(isset($sidebarNavigation))
                        <nav class="p-4 border-b">
                            <h3 class="font-semibold text-gray-900 mb-3">{{ $sidebarTitle ?? 'Navegação' }}</h3>
                            <div class="space-y-2">
                                {{ $sidebarNavigation }}
                            </div>
                        </nav>
                    @endif

                    <!-- Quick Actions -->
                    @if(isset($quickActions))
                        <div class="p-4 border-b">
                            <h4 class="font-semibold text-gray-900 mb-3">Ações Rápidas</h4>
                            <div class="space-y-2">
                                {{ $quickActions }}
                            </div>
                        </div>
                    @endif

                    <!-- Sidebar Footer/Summary -->
                    @if(isset($sidebarFooter))
                        <div class="p-4 mt-auto">
                            {{ $sidebarFooter }}
                        </div>
                    @endif
                </div>
            </aside>

            <!-- Overlay for mobile -->
            <div x-show="sidebarOpen" 
                 @click="sidebarOpen = false" 
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"></div>
        @endif

        <!-- Main Content -->
        <main class="flex-1 {{ isset($showSidebar) && $showSidebar ? 'lg:ml-0' : '' }}">
            <!-- Page Header (optional) -->
            @if(isset($pageHeader))
                <div class="bg-white shadow-sm border-b px-6 py-4">
                    {{ $pageHeader }}
                </div>
            @endif

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mx-6 mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mx-6 mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Page Content -->
            <div class="{{ $contentClasses ?? 'p-6' }}">
                {{ $slot }}
            </div>
        </main>

        <!-- Right Panel (optional - for cart, details, etc.) -->
        @if(isset($rightPanel))
            <aside class="w-96 bg-white shadow-lg border-l hidden xl:block">
                {{ $rightPanel }}
            </aside>
        @endif
    </div>

    <!-- Global Modals -->
    @if(isset($modals))
        {{ $modals }}
    @endif

    <!-- Loading Indicator -->
    <div wire:loading.delay class="fixed top-4 right-4 z-50">
        <div class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Carregando...</span>
        </div>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>