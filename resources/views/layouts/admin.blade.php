@extends('layouts.base')

@section('sidebar')
<aside class="w-64 bg-gray-200 h-screen p-4">
    <x-menu activate-by-route>
        <x-menu-item title="Dashboard" icon="o-home" link="/admin/dashboard" />
        <x-menu-item title="User Management" icon="o-users" link="/admin/users" />
        <x-menu-item title="Reports" icon="o-chart-bar" link="/admin/reports" />
        <x-menu-item title="Settings" icon="o-cog" link="/admin/settings" />
    </x-menu>
</aside>
@endsection

@section('content')
@yield('admin-content')
@endsection
