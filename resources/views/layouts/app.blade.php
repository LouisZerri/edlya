<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#4f46e5">
    <title>@yield('title', 'Edlya')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 min-h-screen safe-area-x flex flex-col">

    <x-toast />

    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                {{-- Logo --}}
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-10 h-10">
                            <path d="M50 10 L88 42 L88 88 L12 88 L12 42 Z" fill="none" stroke="#4f46e5"
                                stroke-width="5" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M6 45 L50 10 L94 45" fill="none" stroke="#4f46e5" stroke-width="5"
                                stroke-linecap="round" stroke-linejoin="round" />
                            <ellipse cx="50" cy="58" rx="20" ry="14" fill="none"
                                stroke="#4f46e5" stroke-width="4" />
                            <circle cx="50" cy="58" r="7" fill="#4f46e5" />
                        </svg>
                        <div>
                            <span class="text-xl font-bold text-primary-600">Edlya</span>
                            <span class="hidden sm:block text-xs text-slate-500">Propulsé par GEST'IMMO</span>
                        </div>
                    </a>
                </div>

                {{-- Menu desktop --}}
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('dashboard') }}"
                        class="text-sm {{ request()->routeIs('dashboard') ? 'text-primary-600' : 'text-slate-600 hover:text-primary-600' }} transition-colors">
                        Tableau de bord
                    </a>
                    <a href="{{ route('logements.index') }}"
                        class="text-sm {{ request()->routeIs('logements.*') ? 'text-primary-600' : 'text-slate-600 hover:text-primary-600' }} transition-colors">
                        Logements
                    </a>
                    <a href="{{ route('etats-des-lieux.index') }}"
                        class="text-sm {{ request()->routeIs('etats-des-lieux.*') ? 'text-primary-600' : 'text-slate-600 hover:text-primary-600' }} transition-colors">
                        États des lieux
                    </a>

                    <div class="h-5 w-px bg-slate-200"></div>

                    {{-- Bouton Aide --}}
                    <button type="button" data-faq-open
                        class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors cursor-pointer"
                        title="Aide & FAQ">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>

                    <span class="text-sm text-slate-600">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="text-sm text-slate-600 hover:text-primary-600 transition-colors cursor-pointer">
                            Déconnexion
                        </button>
                    </form>
                </div>

                {{-- Bouton menu mobile --}}
                <div class="flex items-center md:hidden">
                    {{-- Bouton Aide mobile --}}
                    <button type="button" data-faq-open
                        class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors cursor-pointer mr-2"
                        title="Aide & FAQ">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                    
                    <button type="button" id="mobile-menu-button" 
                        class="p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"
                        aria-expanded="false"
                        aria-label="Menu principal">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Menu mobile --}}
        <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200">
            <div class="px-4 py-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('dashboard') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                    Tableau de bord
                </a>
                <a href="{{ route('logements.index') }}"
                    class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('logements.*') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                    Logements
                </a>
                <a href="{{ route('etats-des-lieux.index') }}"
                    class="block px-3 py-2 rounded-lg text-sm {{ request()->routeIs('etats-des-lieux.*') ? 'bg-primary-50 text-primary-600' : 'text-slate-600 hover:bg-slate-50' }} transition-colors">
                    États des lieux
                </a>
            </div>
            <div class="px-4 py-3 border-t border-slate-200">
                <div class="px-3 py-2 text-sm text-slate-500">{{ Auth::user()->name }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-3 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 safe-area-bottom flex-1 w-full">
        @yield('content')
    </main>

    @yield('footer')

    {{-- FAQ Modal --}}
    <x-faq-modal />

</body>

</html>