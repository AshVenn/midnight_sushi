<!-- resources/views/checkout/cancel.blade.php -->
    
<x-card title="Paiement Annulé" class="max-w-2xl mx-auto">
        <p>Le paiement a été annulé. Vous pouvez réessayer ou vérifier votre panier.</p>
        <x-button label="Revenir au panier" link="{{ route('customer.orders.index') }}" class="mt-4" />
</x-card>