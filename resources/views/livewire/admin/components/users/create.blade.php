<x-card title="Créer un Utilisateur">
    <x-form wire:submit.prevent="save">
        <x-input wire:model="first_name" label="Prénom" placeholder="Entrez le prénom" />
        <x-input wire:model="last_name" label="Nom" placeholder="Entrez le nom" />
        <x-input wire:model="email" label="Email" type="email" placeholder="Entrez l'adresse email" />
        <x-input wire:model="password" label="Mot de Passe" type="password" placeholder="Entrez le mot de passe" />
        <x-select wire:model="role" label="Rôle" :options="[ 
            ['id' => 'admin', 'name' => 'Administrateur'], 
            ['id' => 'manager', 'name' => 'Responsable'], 
            ['id' => 'customer', 'name' => 'Client'], 
            ['id' => 'delivery_person', 'name' => 'Livreur'] 
        ]" placeholder="Sélectionnez un rôle" />
        <x-file wire:model="avatar" label="Avatar" accept="image/*" />

        @if ($avatar)
            <img src="{{ $avatar->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="Aperçu" />
        @endif

        <x-slot:actions>
            <x-button label="Annuler" link="{{ route('admin.users.index') }}" />
            <x-button label="Créer l'Utilisateur" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
