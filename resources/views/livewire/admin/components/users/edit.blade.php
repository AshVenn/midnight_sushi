<x-card title="Modifier l'Utilisateur">
    <x-form wire:submit.prevent="save">
        <x-input wire:model="first_name" label="Prénom" placeholder="Entrez le prénom" />
        <x-input wire:model="last_name" label="Nom" placeholder="Entrez le nom" />
        <x-input wire:model="email" label="Email" type="email" placeholder="Entrez l'adresse email" />
        <x-select wire:model="role" label="Rôle" :options="[ 
            ['id' => 'admin', 'name' => 'Administrateur'], 
            ['ide' => 'customer', 'name' => 'Client'], 
            ['id' => 'manager', 'name' => 'Responsable'], 
            ['id' => 'delivery_person', 'name' => 'Livreur'] 
        ]" placeholder="Sélectionnez un rôle" />
        <x-file wire:model="avatar" label="Avatar" accept="image/*" />

        @if ($oldAvatarUrl)
            <img src="{{ $oldAvatarUrl }}" class="h-40 rounded-lg mt-2" alt="Avatar actuel" />
        @endif

        @if ($avatar)
            <img src="{{ $avatar->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="Aperçu du nouvel avatar" />
        @endif

        <x-slot:actions>
            <x-button label="Annuler" link="{{ route('admin.users.index') }}" />
            <x-button label="Mettre à Jour" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
