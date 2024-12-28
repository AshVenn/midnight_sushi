<!-- resources/views/livewire/auth/register.blade.php -->
<div class="w-full max-w-md mx-auto p-8 rounded shadow-sm">
    <h2 class="text-xl font-bold text-center mb-4">Créez Votre Compte</h2>

    @if (session()->has('success'))
        <div class="bg-green-100 text-green-700 p-4 mb-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <x-form wire:submit="register">
        <x-input label="Prénom" wire:model="first_name" required />
        <x-input label="Nom" wire:model="last_name" required />
        <x-input label="Email" type="email" wire:model="email" required />
        <x-input label="Mot de Passe" type="password" wire:model="password" required />

        @php
            $roles = [
                ['id' => 'admin', 'name' => 'Administrateur'],
                ['id' => 'customer', 'name' => 'Client'],
                ['id' => 'manager', 'name' => 'Responsable'],
                ['id' => 'delivery_person', 'name' => 'Livreur'],
            ];
        @endphp

        <x-select label="Rôle" :options="$roles" placeholder="Sélectionnez un Rôle" wire:model="role" required />

        <div class="mt-6">
            <x-button label="S'inscrire" class="btn-primary w-full" type="submit" spinner="register" />
        </div>
    </x-form>
</div>
