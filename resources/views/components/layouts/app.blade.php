<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    {{-- Navbar --}}
    <x-nav sticky full-width class="sticky top-0 z-50">
    <x-slot:brand class="z-50">
            <label for="main-drawer" class="lg:hidden mr-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>

            <a href="/">
            <img
                src="/images/midnight_sushi/Logo midnight sushi Light.svg"
                alt="Desktop Light Logo"
                class="hidden md:block h-16 dark:hidden"  
            />

            <img
                src="/images/midnight_sushi/Logo midnight sushi Dark.svg"
                alt="Desktop Dark Logo"
                class="hidden md:hidden md:dark:block  h-16"
            />

            <img
                src="/images/midnight_sushi/Midnight Sushi Mobile Light.svg"
                alt="Mobile Light Logo"
                class="block h-12 md:hidden dark:hidden"  
            />

        
            <img
                    src="/images/midnight_sushi/Midnight Sushi Mobile Dark.svg"
                    alt="Mobile Dark Logo"
                    class="hidden h-12 md:hidden dark:block dark:md:hidden"
                />

        </a>

        </x-slot:brand>


        <x-slot:actions>
            <x-theme-toggle class="btn btn-circle btn-ghost" />
            @unless(auth()->check() && in_array(auth()->user()->role, ['admin', 'manager', 'delivery_person']))
            <x-button icon="o-shopping-bag" class="btn-circle btn-ghost" link="/menus-products" />
                @livewire('cart')
            @endunless
            @if(auth()->check())
            <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button icon="o-power" type="submit" class="btn-circle btn-ghost btn-xs" tooltip-left="Déconnexion" no-wire-navigate />
            </form>
            @else
                <div class="hidden md:flex space-x-6">
                    <x-button icon="o-user" link="/login" class=" btn-circle btn-ghost ml-2 " />
                </div>


            @endif
        </x-slot:actions>
    </x-nav>

    {{-- Main Layout --}}
    <x-main with-nav full-width>
        {{-- Sidebar --}}
        <x-slot:sidebar drawer="main-drawer" collapsible class="bg-base-200">
            @if($user = auth()->user())
                {{-- User Info --}}
            <x-list-item :item="$user" value="first_name" sub-value="email" no-separator no-hover class="pt-2">
                <x-slot:actions>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button icon="o-power" type="submit" class="btn-circle btn-ghost btn-xs" tooltip-left="Déconnexion" no-wire-navigate />
                    </form>
                </x-slot:actions>
            </x-list-item>
            

                <x-menu-separator />

                {{-- Role-Based Sidebar --}}
                @switch($user->role)
                    @case('admin')
                    <x-menu activate-by-route>
                        <x-menu-item title="Tableau de Bord" icon="o-home" link="/admin/dashboard" />
                        <x-menu-item title="Utilisateurs" icon="o-users" link="/admin/users" />
                        <x-menu-item title="Catégories" icon="o-clipboard-document-list" link="/admin/categories" />
                        <x-menu-item title="Produits" icon="o-shopping-bag" link="/admin/products" />
                        <x-menu-item title="Menus" icon="o-swatch" link="/admin/menus" />
                        <x-menu-sub title="Gestion" icon="o-user-group">
                            <x-menu-item title="Commandes" icon="s-shopping-cart" link="/admin/orders" />
                            <x-menu-item title="Livraisons" icon="o-truck" link="/admin/deliveries" />
                        </x-menu-sub>
                        <x-menu-item title="Demandes de contact" icon="o-information-circle" link="/admin/contactinquiries" />
                        <x-menu-item title="Paramètres" icon="o-cog-6-tooth" link="/admin/settings" />
                    </x-menu>

                        @break

                    @case('customer')
                        <x-menu activate-by-route>
                            <x-menu-item title="Tableau de Bord" icon="o-home" link="/customer/dashboard" />
                            <x-menu-item title="Orders" icon="o-shopping-cart" link="/customer/orders" />
                            <x-menu-item title="Profile" icon="o-user" link="/customer/profile" />
                            <x-menu-item title="Support" icon="o-information-circle" link="/customer/support" />
                        </x-menu>
                        @break

                    @case('manager')
                        <x-menu activate-by-route>
                            <x-menu-item title="Tableau de Bord" icon="o-home" link="/manager/dashboard" />
                            <x-menu-item title="Utilisateurs" icon="o-users" link="/manager/users" />
                            <x-menu-item title="Catégories" icon="o-clipboard-document-list" link="/manager/categories" />
                            <x-menu-item title="Produits" icon="o-shopping-bag" link="/manager/products" />
                            <x-menu-item title="Menus" icon="o-swatch" link="/manager/menus" />
                            <x-menu-sub title="Gestion" icon="o-user-group">
                                <x-menu-item title="Commandes" icon="s-shopping-cart" link="/manager/orders" />
                                <x-menu-item title="Livraisons" icon="o-truck" link="/manager/deliveries" />
                            </x-menu-sub>
                            <x-menu-item title="Demandes de contact" icon="o-information-circle" link="/manager/contactinquiries" />
                            <x-menu-item title="Paramètres" icon="o-cog-6-tooth" link="/manager/settings" />
                        </x-menu>

                        @break

                        @case('delivery_person')
                            <x-menu activate-by-route>
                            <x-menu-item title="Tableau de Bord" icon="o-home" link="/deliveryperson/dashboard" />
                                <x-menu-item title="Livraisons" icon="o-truck" link="/deliveryperson/deliveries" />
                                <x-menu-item title="Livraisons Passées" icon="o-document-check" link="/deliveryperson/pastdeliveries" />
                                <x-menu-item title="Paramètres" icon="o-cog-6-tooth" link="/deliveryperson/settings" />
                            </x-menu>
                        @break

                    @default
                        <x-menu activate-by-route>
                      
                        <x-menu-item 
                            title="Home" 
                            icon="o-home" 
                            x-data 
                            @click="location.reload()" 
                        />
                    
                        </x-menu>
                @endswitch
            @else
                {{-- Default Sidebar for Guests --}}
                <x-menu activate-by-route>
                                
                        <x-menu-item 
                            title="Home" 
                            icon="o-home" 
                            x-data 
                            @click="window.location.href = '/';" 
                        />
                                    
                    <x-menu-item title="Menus & Produits" icon="o-swatch" link="{{ route('menus-products') }}" />
                    <x-menu-item title="À propos" icon="o-information-circle" link="/about" />
                    <x-menu-item title="Faq" icon="o-question-mark-circle" link="/faq" />

                </x-menu>
            @endif
        </x-slot:sidebar>

        {{-- Main Content --}}
        <x-slot:content>
            {{ $slot }}
        </x-slot:content>
    </x-main>

    {{-- Toast Notifications --}}
    <x-toast />
</body>
</html>
