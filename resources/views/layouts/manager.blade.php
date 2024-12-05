@extends('layouts.base')

@section('sidebar')
<aside class="w-64 bg-gray-200 h-screen p-4">
    <x-menu activate-by-route>
        <x-menu-item title="Dashboard" icon="o-home" link="/manager/dashboard" />
        <x-menu-item title="Performance Tracker" icon="o-chart-bar" link="/manager/performance" />
        <x-menu-item title="Inventory Management" icon="o-archive" link="/manager/inventory" />
        <x-menu-item title="Settings" icon="o-cog" link="/manager/settings" />
    </x-menu>
</aside>
@endsection

@section('content')
@yield('manager-content')
@endsection
