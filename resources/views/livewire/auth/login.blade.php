<!-- resources/views/livewire/auth/login.blade.php -->
<div class="max-w-md mx-auto p-6 md:p-8">
    <h2 class="text-2xl font-semibold text-center mb-4">Bienvenue de Retour !</h2>

    <x-form wire:submit="login" class="space-y-4">
        <x-input label="Email" type="email" wire:model="email" required autocomplete="off" />
        <x-input label="Mot de Passe" type="password" wire:model="password" required />

        <x-slot:actions>
            <div class="flex flex-col sm:flex-row sm:justify-between gap-2 mt-4">
                <x-button label="Se Connecter" class="btn-primary w-full sm:w-auto" type="submit" variant="primary" spinner="login" />
                <x-button label="S'inscrire" link="/register" variant="secondary" class="w-full sm:w-auto" />
                <x-button label="Mot de passe oublié ?" link="{{ route('password.request') }}" class="btn-ghost w-full sm:w-auto" />
            </div>
        </x-slot:actions>
    </x-form>
</div>
