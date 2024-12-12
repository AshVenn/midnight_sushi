<div>
    <x-card title="Paramètres du Livreur">
        <x-form wire:submit.prevent="save">
            {{-- Prénom --}}
            <x-input wire:model="first_name" label="Prénom" placeholder="Entrez votre prénom" />

            {{-- Nom --}}
            <x-input wire:model="last_name" label="Nom" placeholder="Entrez votre nom" />

            {{-- Email --}}
            <x-input wire:model="email" label="Email" type="email" placeholder="Entrez votre email" />

            {{-- Téléphone --}}
            <x-input wire:model="phone_number" label="Numéro de Téléphone" placeholder="Entrez votre numéro de téléphone" />

            {{-- Mot de Passe --}}
            <x-input wire:model="password" label="Mot de Passe" type="password" placeholder="Entrez un nouveau mot de passe (facultatif)" />
            <x-input wire:model="confirm_password" label="Confirmer le Mot de Passe" type="password" placeholder="Confirmez votre mot de passe" />

            {{-- Téléchargement de l'Avatar --}}
            <x-file wire:model="avatar" label="Photo de Profil" accept="image/*" />

            {{-- Aperçu de l'Avatar --}}
            @if ($oldAvatarUrl)
                <img src="{{ $oldAvatarUrl }}" class="h-40 rounded-lg mt-2" alt="Avatar Actuel" />
            @endif

            @if ($avatar)
                <img src="{{ $avatar->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="Aperçu de l'Avatar" />
            @endif

            {{-- Boutons d'Action --}}
            <x-slot:actions>
                <x-button label="Sauvegarder les Modifications" type="submit" class="btn-primary" />
            </x-slot:actions>
        </x-form>

        {{-- Toast Message --}}
        <x-toast />
    </x-card>
</div>
