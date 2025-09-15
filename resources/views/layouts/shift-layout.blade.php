{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' - RestauFlow' : 'RestauFlow' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    },
                    backgroundImage: {
                        'gradient-dark': 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)'
                    }
                }
            }
        }
    </script>

    @stack('styles')
</head>
<body class="font-inter bg-zinc-50 dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 min-h-screen transition-all duration-500">
    
    {{-- Background decorativo apenas no dark mode --}}
    <div class="fixed inset-0 overflow-hidden dark:block hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-purple-500/5 rounded-full blur-3xl"></div>
        <div class="absolute top-3/4 right-1/4 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 right-1/3 w-72 h-72 bg-violet-500/5 rounded-full blur-3xl"></div>
    </div>
    {{-- Header flutuante --}}
    <nav class="fixed top-2 left-1/2 transform -translate-x-1/2 z-50 bg-white dark:bg-zinc-800 backdrop-blur-lg border border-zinc-200 dark:border-zinc-700 rounded-2xl px-4 py-2 w-full max-w-7xl  shadow-lg transition-all duration-500">
        <div class="flex items-center justify-between">
            {{-- Logo --}}
            <div class="flex items-center space-x-3">
                <div class="w-6 h-6 bg-primary-500 rounded-lg flex items-center justify-center shadow-sm">
                    <span class="material-icons text-zinc-900 dark:text-white text-md">trending_up</span>
                </div>
                <h1 class="text-lg font-bold text-zinc-900 dark:text-white">RestauFlow</h1>
            </div>

            {{-- Navigation Links --}}
            <div class="hidden md:flex items-center space-x-6">
                <a href="#" class="text-zinc-700 hover:text-primary-600 dark:text-zinc-300 dark:hover:text-white text-sm font-medium transition-colors">Dashboard</a>
                <a href="#" class="text-zinc-700 hover:text-primary-600 dark:text-zinc-300 dark:hover:text-white text-sm font-medium transition-colors">Profile</a>
                <a href="#" class="text-zinc-700 hover:text-primary-600 dark:text-zinc-300 dark:hover:text-white text-sm font-medium transition-colors">Sign Up</a>
                <button onclick="toggleTheme()" class="p-1 text-zinc-700 hover:text-primary-600 dark:text-zinc-300 dark:hover:text-white focus:outline-none transition-colors rounded-md hover:bg-zinc-100 dark:hover:bg-zinc-700">
                    <span id="themeIcon" class="material-icons text-sm transition-transform duration-300">dark_mode</span>
                </button>
            </div>

            {{-- Mobile --}}
            <div class="md:hidden">
                <button onclick="toggleTheme()" class="p-2 text-zinc-700 hover:text-primary-600 dark:text-zinc-300 dark:hover:text-white focus:outline-none transition-colors rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700">
                    <span class="material-icons text-lg">dark_mode</span>
                </button>
            </div>
        </div>
    </nav>

    {{-- Conteúdo Principal --}}
    <main class="relative z-10 pt-32 pb-20 max-w-7xl mx-auto">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="relative z-10 bg-white dark:bg-zinc-800 border-t border-zinc-200 dark:border-zinc-700 transition-all duration-500">
        <div class="max-w-6xl mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 md:mb-0">
                    © {{ date('Y') }}, made with ❤️ by Yuvi Matique for a better web.
                </p>
                <div class="flex space-x-8">
                    <a href="#" class="text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white text-sm transition-colors font-medium">YAM</a>
                    <a href="#" class="text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white text-sm transition-colors font-medium">Aly</a>
                    <a href="#" class="text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white text-sm transition-colors font-medium">Blog</a>
                    {{-- <a href="#" class="text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white text-sm transition-colors font-medium">License</a> --}}
                </div>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const themeIcon = document.getElementById('themeIcon');
            
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                themeIcon.textContent = 'dark_mode';
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.textContent = 'light_mode';
            }
        }

        function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const themeIcon = document.getElementById('themeIcon');

            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
                if (themeIcon) themeIcon.textContent = 'light_mode';
            } else {
                if (themeIcon) themeIcon.textContent = 'dark_mode';
            }
        }

        document.addEventListener('DOMContentLoaded', initTheme);
    </script>

    @stack('scripts')
</body>
</html>