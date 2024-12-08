@extends('layouts.base')

@section('sidebar')
<aside class="w-64 h-screen p-4 bg-gray-100">
    <x-menu activate-by-route>
        {{-- Dashboard --}}
        <x-menu-item 
            title="Dashboard" 
            icon="o-home" 
            link="/admin/dashboard" 
        />

        {{-- User Management --}}
        <x-menu-item 
            title="User Management" 
            icon="o-users" 
            link="{{ route('admin.users.index') }}" 
        />

        {{-- Product Management --}}
        <x-menu-item 
            title="Product Management" 
            icon="o-box" 
            link="{{ route('admin.products.index') }}" 
        />

        {{-- Category Management --}}
        <x-menu-item 
            title="Category Management" 
            icon="o-tag" 
            link="{{ route('admin.categories.index') }}" 
        />

        {{-- Orders --}}
        <x-menu-item 
            title="Orders" 
            icon="o-receipt" 
            link="{{ route('admin.orders.index') }}" 
        />

        {{-- Delivery Assignments --}}
        <x-menu-item 
            title="Delivery Assignments" 
            icon="o-truck" 
            link="{{ route('admin.deliveries.index') }}" 
        />

        {{-- Reports --}}
        <x-menu-item 
            title="Reports" 
            icon="o-chart-bar" 
            link="/admin/reports" 
        />

        {{-- Settings --}}
        <x-menu-item 
            title="Settings" 
            icon="o-cog" 
            link="/admin/settings" 
        />
    </x-menu>
</aside>
@endsection

@section('content')
@yield('admin-content')
@endsection
