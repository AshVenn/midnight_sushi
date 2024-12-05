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
    {{-- Navbar --}}
    <x-nav sticky full-width>
        <x-slot:brand>
            {{-- Drawer toggle for small screens --}}
            <label for="main-drawer" class="lg:hidden mr-3 cursor-pointer">
                <x-icon name="o-bars-3" />
            </label>

            {{-- Brand --}}
            <div class="text-2xl font-extrabold italic text-primary">Midnight Sushi</div>
        </x-slot:brand>

        {{-- Navbar actions --}}
        <x-slot:actions class="hidden md:flex items-center space-x-3">

            <x-button label="About" link="/about" class="btn-ghost font-bold" />
            <x-button label="Contact" link="/contact" class="btn-ghost font-bold" />
            <x-theme-toggle class="btn btn-circle" />
            <x-button link="/login"  icon="o-user" class="btn-circle btn-primary" responsive />
 
        </x-slot:actions>
    </x-nav>

    <x-main with-nav full-width>
        {{-- Sidebar (collapsible drawer on small screens) --}}
        <x-slot:sidebar drawer="main-drawer" class="bg-base-200 block lg:hidden">
            <div class="p-4">
                {{-- Sidebar menu items --}}
                <x-menu activate-by-route>
                    <x-menu-item title="Home" icon="o-home" link="/" />
                    <x-menu-item title="About" icon="o-information-circle" link="/about" />
                    <x-menu-item title="Login" icon="o-user" link="/login" />
                </x-menu>
            </div>
        </x-slot:sidebar>

        {{-- Main content area --}}
        <x-slot:content>

            {{-- Main Content --}}
            @yield('content')
        </x-slot:content>
    </x-main>

    <x-toast />
    @livewireScripts
</body>
</html>
