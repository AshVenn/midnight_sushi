<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased">
    {{-- Shared Navbar --}}
    <x-nav sticky full-width>
        <x-slot:brand>
            {{-- Drawer toggle for small screens --}}
            <label for="main-drawer" class="lg:hidden mr-3 cursor-pointer">
                <x-icon name="o-bars-3" />
            </label>

            {{-- Brand --}}
            <div class="text-2xl font-extrabold italic text-primary">Midnight Sushi</div>
        </x-slot:brand>
    </x-nav>

    <div class="flex">
        {{-- Sidebar --}}
        @yield('sidebar')

        {{-- Main Content --}}
        <div class="flex-1 p-4">
            @yield('content')
        </div>
    </div>

    @livewireScripts
</body>
</html>
