<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    {{-- Navbar --}}
    <x-nav sticky full-width>
        <x-slot:brand>
            <label for="main-drawer" class="lg:hidden mr-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
            <div class="text-2xl font-bold text-primary">{{ config('app.name') }}</div>
        </x-slot:brand>

        <x-slot:actions>
            <x-button label="About" link="/about" class="btn-ghost font-bold" />
            <x-button label="Contact" link="/contact" class="btn-ghost font-bold" />
            <x-theme-toggle class="btn btn-circle" />
            @if(auth()->check())
                <x-button label="Logout" icon="o-power" link="/logout" class="btn-primary" />
            @else
                <x-button label="Login" icon="o-user" link="/login" class="btn-primary" />
            @endif
        </x-slot:actions>
    </x-nav>

    {{-- Main Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>
</body>
</html>
