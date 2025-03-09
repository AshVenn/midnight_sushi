<!-- resources/views/checkout/success.blade.php -->
<x-app-layout>
    <x-card title="Paiement Réussi" class="max-w-2xl mx-auto">
        <p>Merci pour votre paiement ! Votre commande #{{ $order->order_number }} est confirmée.</p>
        <x-button label="Retour à l'accueil" link="{{ route('home') }}" class="mt-4" />
    </x-card>
</x-app-layout>


