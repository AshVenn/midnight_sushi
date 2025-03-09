<!-- resources/views/checkout/cancel.blade.php -->
<x-app-layout>
    <x-card title="Paiement Annulé" class="max-w-2xl mx-auto">
        <p>Le paiement a été annulé. Vous pouvez réessayer ou vérifier votre panier.</p>
        <x-button label="Revenir au panier" link="{{ route('cart') }}" class="mt-4" />
    </x-card>
</x-app-layout>