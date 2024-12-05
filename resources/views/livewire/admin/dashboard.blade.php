<div>
    <x-card>
        <x-card-header>
            <h1 class="text-2xl font-bold">Admin Dashboard</h1>
        </x-card-header>

        <x-card-body>
            @livewire('admin.components.user-management')
            @livewire('admin.components.reports')
        </x-card-body>
    </x-card>
</div>
