{{-- resources/views/components/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ISP Manager' }} - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Sidebar Desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div
                class="flex grow flex-col gap-y-5 overflow-y-auto bg-white dark:bg-gray-800 px-6 border-r border-gray-200 dark:border-gray-700">
                <!-- Logo -->
                <div class="flex h-16 shrink-0 items-center">
                    <div class="flex items-center space-x-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2L2 6l8 4 8-4-8-4zM2 14l8 4 8-4M2 10l8 4 8-4" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Mr Frango</h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Restauflow</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <!-- Dashboard -->
                        <li>
                            <a href=""
                                class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:text-blue-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' }}"
                                wire:navigate>
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                </svg>
                                Dashboard
                            </a>
                        </li>

                        <!-- Clientes -->
                        {{-- <li>
                            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">Clientes
                            </div>
                            <ul role="list" class="-mx-2 mt-2 space-y-1">
                                <li>
                                    <a href=""
                                        class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 {{ request()->routeIs('customers') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:text-blue-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' }}"
                                        wire:navigate>
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                        Clientes
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                        wire:navigate>
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                        </svg>
                                        Endereços
                                    </a>
                                </li>
                            </ul>
                        </li> --}}

                        <!-- Serviços -->
                        {{-- <li>
                            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">Serviços
                            </div>
                            <ul role="list" class="-mx-2 mt-2 space-y-1">
                                <li>
                                    <a href=""
                                        class=" hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 {{ request()->routeIs('plans') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:text-blue-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' }}"
                                        wire:navigate>
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                                        </svg>
                                        Planos de Internet
                                    </a>
                                </li> --}}

                                <flux:navlist variant="outline">

                                                <flux:navlist.group :heading="__('messages.dashboard.subtitle')" class="grid">

                                                    {{-- Dashboard --}}
                                                    <flux:navlist.item icon="home" :href="route('restaurant.dashboard')" :current="request()->routeIs('restaurant.dashboard')" wire:navigate>{{ __('messages.dashboard.title') }}</flux:navlist.item>

                                                    {{-- Products --}}
                                                    <flux:navlist.item icon="home" :href="route('restaurant.products')" :current="request()->routeIs('restaurant.products')" wire:navigate>{{ __('messages.dashboard.products') }}</flux:navlist.item>

                                                    {{-- Stocks --}}
                                                    <flux:navlist.item icon="home" :href="route('restaurant.stocks')" :current="request()->routeIs('restaurant.stocks')" wire:navigate>{{ __('messages.dashboard.stoks') }}</flux:navlist.item>

                                                </flux:navlist.group>

                                            </flux:navlist>
{{-- 

                                <li>
                                    <a href=""
                                        class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                        wire:navigate>
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M21.75 17.25v-.228a4.5 4.5 0 00-.12-1.03l-2.268-9.64a3.375 3.375 0 00-3.285-2.602H7.923a3.375 3.375 0 00-3.285 2.602l-2.268 9.64a4.5 4.5 0 00-.12 1.03v.228m19.5 0a3 3 0 01-3 3H5.25a3 3 0 01-3-3m19.5 0a3 3 0 00-3-3H5.25a3 3 0 00-3 3" />
                                        </svg>
                                        Equipamentos
                                    </a>
                                </li>
                                <li>
                                    <a href=""
                                        class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                        wire:navigate>
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                        </svg>
                                        Subscrições
                                    </a>
                                </li>
                            </ul>
                        </li> --}}

                        <!-- Financeiro -->
                        {{-- <li>
                            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">
                                Financeiro</div>
                            <ul role="list" class="-mx-2 mt-2 space-y-1">
                                <li>
                                    <a href=""
                                        class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                        wire:navigate>
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        Faturas
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                        wire:navigate>
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                        </svg>
                                        Pagamentos
                                    </a>
                                </li>
                            </ul>
                        </li> --}}

                        <!-- Operações -->
                        {{-- <li>
                            <div class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">
                                Operações</div>
                            <ul role="list" class="-mx-2 mt-2 space-y-1">
                                <li>
                                    <a href="#"
                                        class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                        wire:navigate>
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655-4.653a2.548 2.548 0 010-3.586l.837-.836c.317-.317.751-.487 1.204-.487z" />
                                        </svg>
                                        Ordens de Serviço
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                        wire:navigate>
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                                        </svg>
                                        Tickets de Suporte
                                    </a>
                                </li>
                            </ul>
                        </li> --}}

                        <!-- Relatórios -->
                        {{-- <li>
                            <a href="#"
                                class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                wire:navigate>
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                                </svg>
                                Relatórios
                            </a>
                        </li> --}}
                    </ul>
                </nav>

                <!-- User menu -->
                <div class="mt-auto pb-4">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open"
                            class="flex w-full items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50 dark:text-white dark:hover:bg-gray-800">
                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center">
                                <span class="text-sm font-medium text-white">{{ auth()->user()->initials() }}</span>
                            </div>
                            <span class="sr-only">Seu perfil</span>
                            <span aria-hidden="true" class="flex-1 text-left">{{ auth()->user()->name }}</span>
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute left-0 right-0 bottom-full mb-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2">
                            <a href="{{ route('settings.profile') }}"
                                class="flex items-center gap-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                                wire:navigate>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Meu Perfil
                            </a>
                            <a href="{{ route('settings.appearance') }}"
                                class="flex items-center gap-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                                wire:navigate>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Configurações
                            </a>
                            <hr class="my-2 border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-x-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div x-data="{ open: false }" class="lg:hidden">
            <!-- Mobile menu button -->
            <div
                class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 dark:border-gray-700 dark:bg-gray-800">
                <button type="button" @click="open = true"
                    class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-200 lg:hidden">
                    <span class="sr-only">Abrir sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="flex flex-1 items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="flex h-6 w-6 items-center justify-center rounded bg-blue-600">
                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 2L2 6l8 4 8-4-8-4zM2 14l8 4 8-4M2 10l8 4 8-4" />
                            </svg>
                        </div>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">RestauFlow</span>
                    </div>

                    <!-- Mobile user menu -->
                    <div x-data="{ userOpen: false }" class="relative">
                        <button @click="userOpen = !userOpen"
                            class="flex items-center space-x-2 rounded-full bg-gray-50 p-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-gray-700">
                            <div class="h-6 w-6 rounded-full bg-blue-600 flex items-center justify-center">
                                <span class="text-xs font-medium text-white">{{ auth()->user()->initials() }}</span>
                            </div>
                        </button>

                        <div x-show="userOpen" @click.away="userOpen = false" x-transition
                            class="absolute right-0 top-full mt-2 w-48 rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-gray-800 dark:ring-gray-700">
                            <a href="{{ route('settings.profile') }}"
                                class="flex items-center gap-x-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                                wire:navigate>
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Meu Perfil
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-x-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                    </svg>
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu overlay -->
            <div x-show="open" class="relative z-50 lg:hidden" x-cloak>
                <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-linear duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/80"></div>

                <div class="fixed inset-0 flex">
                    <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform"
                        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in-out duration-300 transform"
                        x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                        class="relative mr-16 flex w-full max-w-xs flex-1">
                        <div x-show="open" x-transition:enter="ease-in-out duration-300"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute left-full top-0 flex w-16 justify-center pt-5">
                            <button type="button" @click="open = false" class="-m-2.5 p-2.5">
                                <span class="sr-only">Fechar sidebar</span>
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-2 dark:bg-gray-800">
                            <div class="flex h-16 shrink-0 items-center">
                                <div class="flex items-center space-x-3">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600">
                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2L2 6l8 4 8-4-8-4zM2 14l8 4 8-4M2 10l8 4 8-4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">ISP Manager
                                        </h1>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Sistema de Gestão</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Same navigation as desktop -->
                            <nav class="flex flex-1 flex-col">
                                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                    <!-- Dashboard -->
                                    <li>
                                        <a href=""
                                            class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-700 hover:text-blue-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700' }}"
                                            wire:navigate @click="open = false">
                                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                                            </svg>
                                            Dashboard
                                        </a>
                                    </li>

                                    <!-- Clientes -->
                                    <li>
                                        <div
                                            class="text-xs font-semibold leading-6 text-gray-400 uppercase tracking-wide">
                                            Clientes</div>
                                        <ul role="list" class="-mx-2 mt-2 space-y-1">
                                            <li>
                                                <a href="#"
                                                    class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                                    wire:navigate @click="open = false">
                                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                                    </svg>
                                                    Clientes
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"
                                                    class="text-gray-700 hover:text-blue-700 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700"
                                                    wire:navigate @click="open = false">
                                                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                                                        stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                                    </svg>
                                                    Endereços
                                                </a>
                                            </li>
                                        </ul>
                                    </li>

                                    <!-- Adicione os outros itens do menu aqui, similares ao desktop -->
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-72">
            <main class="">

                <!-- Page Header -->
                @if (isset($pageTitle) || isset($pageDescription) || isset($breadcrumbs))
                    <header
                        class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 mb-8">
                        <div class="mx-auto px-4 sm:px-6 lg:px-8">


                            <!-- Page Title & Description -->
                            @if (isset($pageTitle) || isset($pageDescription))
                                <div
                                    class="py-2 {{ isset($breadcrumbs) ? 'border-t border-gray-100 dark:border-gray-700' : '' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="min-w-0 flex-1">
                                            @if (isset($pageTitle))
                                                <h1
                                                    class="text-xl font-bold text-gray-900 dark:text-white sm:text-2xl sm:truncate">
                                                    {{ $pageTitle }}
                                                </h1>
                                            @endif
                                            @if (isset($pageDescription))
                                                <p class="mt- text-base text-gray-600 dark:text-gray-400 max-w-4xl">
                                                    {{ $pageDescription }}
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Header Actions Slot (opcional) -->
                                        @if (isset($headerActions))
                                            <div class="mt-4 flex-shrink-0 sm:mt-0 sm:ml-4">
                                                {{ $headerActions }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                             <!-- Breadcrumbs -->
                            @if (isset($breadcrumbs) && count($breadcrumbs) > 0)
                                <nav class="flex pt-2 pb-2" aria-label="Breadcrumb">
                                    <ol role="list" class="flex items-center space-x-2 text-sm">
                                        <li>
                                            <a href=""
                                                class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors"
                                                wire:navigate>
                                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                <span class="sr-only">Dashboard</span>
                                            </a>
                                        </li>
                                        @foreach ($breadcrumbs as $crumb)
                                            <li class="flex items-center">
                                                <svg class="h-4 w-4 text-gray-300 dark:text-gray-600 "
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                                @if ($loop->last)
                                                    <span
                                                        class="font-medium text-gray-700 dark:text-gray-200">{{ $crumb['label'] }}</span>
                                                @else
                                                    <a href="{{ $crumb['url'] }}"
                                                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                                                        wire:navigate>
                                                        {{ $crumb['label'] }}
                                                    </a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ol>
                                </nav>
                            @endif
                        </div>
                    </header>
                @endif
                <div class="mx-auto px-4 sm:px-6 lg:px-8">


                    <!-- Page content -->
                    <div class="space-y-6">
                        {{ $slot }}
                    </div>
                </div>
            </main>
        </div>
    </div>

    @livewireScripts
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}

    <!-- Toast Notifications -->
    <x-toast-notifications />
</body>

</html>
