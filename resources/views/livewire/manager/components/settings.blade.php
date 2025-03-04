<x-card title="Paramètres Manager">
    <x-form wire:submit.prevent="save">
        <x-input wire:model="first_name" label="Prénom" placeholder="Entrez votre prénom" />
        <x-input wire:model="last_name" label="Nom" placeholder="Entrez votre nom" />
        <x-input wire:model="email" label="Email" type="email" placeholder="Entrez votre email" />
        <x-input wire:model="password" label="Mot de Passe" type="password" placeholder="Nouveau mot de passe (facultatif)" />
        <x-input wire:model="confirm_password" label="Confirmez le mot de passe" type="password" placeholder="Confirmez le mot de passe" />
        <x-file wire:model="avatar" label="Photo de Profil" accept="image/*" />
        @if($oldAvatarUrl)
            <img src="{{ $oldAvatarUrl }}" class="h-40 rounded-lg mt-2" alt="Avatar actuel" />
        @endif
        @if($avatar)
            <img src="{{ $avatar->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="Nouvel avatar" />
        @endif
        <x-slot:actions>
            <x-button label="Enregistrer" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
