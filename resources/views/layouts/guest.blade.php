<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Edlya')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
    
    <x-toast />

    <div class="w-full max-w-md px-4">
        <div class="text-center mb-8">
            <a href="/" class="inline-flex flex-col items-center">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-16 h-16">
                    <path d="M50 10 L88 42 L88 88 L12 88 L12 42 Z" fill="none" stroke="#4f46e5" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 45 L50 10 L94 45" fill="none" stroke="#4f46e5" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                    <ellipse cx="50" cy="58" rx="20" ry="14" fill="none" stroke="#4f46e5" stroke-width="4"/>
                    <circle cx="50" cy="58" r="7" fill="#4f46e5"/>
                </svg>
                <span class="text-2xl font-bold text-primary-600 mt-2">Edlya</span>
                <span class="text-xs text-slate-500">Propulsé par GEST'IMMO</span>
            </a>
        </div>

        @yield('content')
    </div>

</body>
</html>