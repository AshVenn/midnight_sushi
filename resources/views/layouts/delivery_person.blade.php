@extends('layouts.base')

@section('sidebar')
<aside class="w-64 bg-gray-200 h-screen p-4">
    <x-menu activate-by-route>
        <x-menu-item title="Dashboard" icon="o-home" link="/delivery-person/dashboard" />
        <x-menu-item title="Delivery List" icon="o-truck" link="/delivery-person/deliveries" />
        <x-menu-item title="Delivery Status" icon="o-check-circle" link="/delivery-person/status" />
        <x-menu-item title="Profile" icon="o-user" link="/delivery-person/profile" />
    </x-menu>
</aside>
@endsection

@section('content')
@yield('delivery-person-content')
@endsection
