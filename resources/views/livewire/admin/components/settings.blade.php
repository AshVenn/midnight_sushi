<x-card title="Paramètres Administrateur">
    <x-form wire:submit.prevent="save">
        {{-- Prénom --}}
        <x-input 
            wire:model="first_name" 
            label="Prénom" 
            placeholder="Entrez votre prénom" 
        />

        {{-- Nom --}}
        <x-input 
            wire:model="last_name" 
            label="Nom" 
            placeholder="Entrez votre nom" 
        />

        {{-- E-mail --}}
        <x-input 
            wire:model="email" 
            label="E-mail" 
            type="email" 
            placeholder="Entrez votre adresse e-mail" 
        />

        {{-- Mot de passe --}}
        <x-input 
            wire:model="password" 
            label="Mot de passe" 
            type="password" 
            placeholder="Entrez un nouveau mot de passe (optionnel)" 
        />
        <x-input 
            wire:model="confirm_password" 
            label="Confirmez le mot de passe" 
            type="password" 
            placeholder="Confirmez votre mot de passe" 
        />

        {{-- Photo de Profil --}}
        <x-file 
            wire:model="avatar" 
            label="Photo de Profil" 
            accept="image/*" 
        />

        {{-- Aperçu de la photo --}}
        @if ($oldAvatarUrl)
            <img 
                src="{{ $oldAvatarUrl }}" 
                class="h-40 rounded-lg mt-2" 
                alt="Avatar actuel" 
            />
        @endif

        @if ($avatar)
            <img 
                src="{{ $avatar->temporaryUrl() }}" 
                class="h-40 rounded-lg mt-2" 
                alt="Aperçu du nouvel avatar" 
            />
        @endif

        {{-- Boutons d'action --}}
        <x-slot:actions>
            <x-button 
                label="Enregistrer les modifications" 
                type="submit" 
                class="btn-primary" 
            />
        </x-slot:actions>
    </x-form>

    {{-- Espace réservé pour le message Toast --}}
    <x-toast />
</x-card>
