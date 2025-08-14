{{-- resources/views/layouts/auth.blade.php --}}
<!DOCTYPE html>
<html lang="pt" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' || false }" 
      x-bind:class="{ 'dark': darkMode }" 
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- Prevent FOUC -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar for webkit browsers */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #1e293b;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* Gradient animation */
        .gradient-animation {
            background: linear-gradient(-45deg, #0f172a, #1e3a8a, #1e40af, #312e81);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* Glass morphism effect */
        .glass-effect {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .glass-effect-dark {
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            background-color: rgba(17, 24, 39, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Blob animations */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        
        .animate-blob {
            animation: blob 7s infinite;
        }
        
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        
        .animation-delay-4000 {
            animation-delay: 4s;
        }

        /* Loading spinner */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Pulse animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Fade in animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }

        /* Custom focus styles */
        .focus-ring {
            transition: all 0.2s ease-in-out;
        }
        
        .focus-ring:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.4);
        }

        /* Custom button hover effects */
        .btn-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-hover:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .btn-hover:active {
            transform: translateY(0);
        }

        /* Floating elements */
        .float {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0% { transform: translatey(0px); }
            50% { transform: translatey(-10px); }
            100% { transform: translatey(0px); }
        }

        /* Glowing effect */
        .glow {
            box-shadow: 0 0 20px rgba(79, 70, 229, 0.3);
        }
        
        .glow:hover {
            box-shadow: 0 0 30px rgba(79, 70, 229, 0.5);
        }
    </style>
</head>
<body class="font-sans antialiased overflow-x-hidden">
    <div class="min-h-screen gradient-animation relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-black/20">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="1"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-30"></div>
        </div>

        <!-- Floating Geometric Shapes -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Large floating shapes -->
            <div class="absolute top-1/4 left-1/4 w-32 h-32 border border-white/10 rounded-full float"></div>
            <div class="absolute top-1/3 right-1/4 w-24 h-24 border border-white/10 rounded-lg rotate-45 float animation-delay-2000"></div>
            <div class="absolute bottom-1/4 left-1/3 w-20 h-20 border border-white/10 rounded-full float animation-delay-4000"></div>
            
            <!-- Small floating dots -->
            <div class="absolute top-1/2 left-1/6 w-2 h-2 bg-white/20 rounded-full animate-pulse"></div>
            <div class="absolute top-1/4 right-1/6 w-3 h-3 bg-white/20 rounded-full animate-pulse animation-delay-2000"></div>
            <div class="absolute bottom-1/3 right-1/3 w-2 h-2 bg-white/20 rounded-full animate-pulse animation-delay-4000"></div>
        </div>

        <!-- Main Content Container -->
        <div class="relative z-10 min-h-screen flex items-center justify-center p-4">
            <!-- Content Slot -->
            {{ $slot }}
        </div>

        <!-- Footer Information -->
        <div class="absolute bottom-4 left-4 right-4 z-10">
            <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-slate-400/80 space-y-2 sm:space-y-0">
                <div class="flex items-center space-x-4">
                    <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                    <span class="hidden sm:inline">•</span>
                    <span class="hidden sm:inline">Sistema de Gestão Empresarial</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="hover:text-slate-300 transition-colors">Privacidade</a>
                    <span>•</span>
                    <a href="#" class="hover:text-slate-300 transition-colors">Termos</a>
                    <span>•</span>
                    <a href="#" class="hover:text-slate-300 transition-colors">Suporte</a>
                </div>
            </div>
        </div>

        <!-- Version Info (only in development) -->
        @if(app()->environment('local'))
            <div class="absolute top-4 right-4 z-10">
                <div class="bg-white/10 backdrop-blur-sm rounded-lg px-3 py-1 text-xs text-slate-300 border border-white/20">
                    v{{ config('app.version', '1.0.0') }} - DEV
                </div>
            </div>
        @endif
    </div>

    @livewireScripts
    
    <!-- Alpine.js -->
    {{-- <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script> --}}

    <!-- Additional Scripts -->
    @stack('scripts')
    
    <script>
        // Enhanced form interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus first input with slight delay for better UX
            const firstInput = document.querySelector('input[type="email"], input[type="text"]');
            if (firstInput) {
                setTimeout(() => {
                    firstInput.focus();
                    firstInput.select();
                }, 200);
            }
            
            // Enhanced loading states
            document.addEventListener('livewire:navigate', function() {
                document.body.style.cursor = 'wait';
                const loadingOverlay = document.createElement('div');
                loadingOverlay.id = 'loading-overlay';
                loadingOverlay.className = 'fixed inset-0 bg-black/20 backdrop-blur-sm z-50 flex items-center justify-center';
                loadingOverlay.innerHTML = `
                    <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-6 border border-white/20">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-white mx-auto"></div>
                        <p class="text-white text-sm mt-3">Carregando...</p>
                    </div>
                `;
                document.body.appendChild(loadingOverlay);
            });
            
            document.addEventListener('livewire:navigated', function() {
                document.body.style.cursor = 'default';
                const loadingOverlay = document.getElementById('loading-overlay');
                if (loadingOverlay) {
                    loadingOverlay.remove();
                }
            });
            
            // Form validation enhancements
            const inputs = document.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.classList.add('border-red-400');
                        this.classList.remove('border-white/30');
                    } else {
                        this.classList.remove('border-red-400');
                        this.classList.add('border-green-400');
                    }
                });
                
                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('border-red-400');
                        this.classList.add('border-green-400');
                    }
                });
            });
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Alt + L for login focus
            if (e.altKey && e.key === 'l') {
                e.preventDefault();
                const loginInput = document.querySelector('input[type="email"]');
                if (loginInput) {
                    loginInput.focus();
                    loginInput.select();
                }
            }
            
            // Escape to clear form
            if (e.key === 'Escape') {
                const inputs = document.querySelectorAll('input');
                inputs.forEach(input => {
                    if (input.type !== 'submit' && input.type !== 'button') {
                        input.value = '';
                        input.classList.remove('border-red-400', 'border-green-400');
                        input.classList.add('border-white/30');
                    }
                });
            }
        });
        
        // Disable context menu on production
        @if(!app()->environment('local'))
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        @endif
        
        // Enhanced security - disable certain shortcuts in production
        @if(!app()->environment('local'))
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || 
                (e.ctrlKey && e.shiftKey && e.key === 'I') || 
                (e.ctrlKey && e.shiftKey && e.key === 'C') || 
                (e.ctrlKey && e.key === 'u')) {
                e.preventDefault();
            }
        });
        @endif
    </script>
</body>
</html>