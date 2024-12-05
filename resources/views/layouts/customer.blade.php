@extends('layouts.base')

@section('sidebar')
<aside class="w-64 bg-gray-200 h-screen p-4">
    <x-menu activate-by-route>
        <x-menu-item title="Dashboard" icon="o-home" link="/customer/dashboard" />
        <x-menu-item title="Order History" icon="o-shopping-cart" link="/customer/orders" />
        <x-menu-item title="Browse Products" icon="o-search" link="/customer/products" />
        <x-menu-item title="Account Settings" icon="o-user-circle" link="/customer/account" />
    </x-menu>
</aside>
@endsection

@section('content')
@yield('customer-content')
@endsection
