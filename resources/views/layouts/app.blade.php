<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'EcoScan AI')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
    <header class="bg-green-800 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <nav class="flex flex-wrap items-center justify-between gap-3 py-4">
                <a href="{{ route('dashboard') }}" class="text-lg font-semibold sm:text-xl">♻ EcoScan AI</a>
                <div class="flex flex-wrap gap-x-5 gap-y-2 text-sm font-medium">
                    <a href="{{ route('dashboard') }}"
                       class="{{ request()->routeIs('dashboard') ? 'underline underline-offset-8' : 'opacity-80 hover:opacity-100' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('scan') }}"
                       class="{{ request()->routeIs('scan*') ? 'underline underline-offset-8' : 'opacity-80 hover:opacity-100' }}">
                        Scan
                    </a>
                    <a href="{{ route('challenges') }}"
                       class="{{ request()->routeIs('challenges') ? 'underline underline-offset-8' : 'opacity-80 hover:opacity-100' }}">
                        Tantangan
                    </a>
                    <a href="{{ route('achievements') }}"
                       class="{{ request()->routeIs('achievements') ? 'underline underline-offset-8' : 'opacity-80 hover:opacity-100' }}">
                        Pencapaian
                    </a>
                    <a href="{{ route('leaderboard') }}"
                       class="{{ request()->routeIs('leaderboard') ? 'underline underline-offset-8' : 'opacity-80 hover:opacity-100' }}">
                        Peringkat
                    </a>
                    <a href="{{ route('history') }}"
                       class="{{ request()->routeIs('history') ? 'underline underline-offset-8' : 'opacity-80 hover:opacity-100' }}">
                        Riwayat
                    </a>
                    <a href="{{ route('rewards') }}"
                       class="{{ request()->routeIs('rewards*') ? 'underline underline-offset-8' : 'opacity-80 hover:opacity-100' }}">
                        Hadiah
                    </a>
                    <a href="{{ route('profile') }}"
                       class="{{ request()->routeIs('profile*') ? 'underline underline-offset-8' : 'opacity-80 hover:opacity-100' }}">
                        Profil
                    </a>
                </div>
            </nav>

            <div class="pb-6 pt-2 lg:pb-10">
                @yield('header')
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
        @yield('content')
    </main>

    
</body>
</html>