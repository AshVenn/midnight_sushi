<x-card title="Réinitialiser le mot de passe">
    <x-form method="POST" action="{{ route('password.update') }}">
        @csrf

        <!-- Pass the token securely -->
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <x-input label="Adresse Email" name="email" type="email" required autofocus autocomplete="off"/>
        <x-input label="Nouveau mot de passe" name="password" type="password" required />
        <x-input label="Confirmer le mot de passe" name="password_confirmation" type="password" required />

        <x-button label="Réinitialiser le mot de passe" type="submit" variant="primary" class="w-full" />
    </x-form>
</x-card>
