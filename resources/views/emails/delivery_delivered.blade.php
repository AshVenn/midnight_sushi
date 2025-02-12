<x-mail::message>
# Votre commande a été livrée

Bonjour {{ $delivery->order->customer->first_name }},

Votre commande **#{{ $delivery->order_id }}** a été livrée avec succès.

**Détails de la livraison :**
- **Numéro de livraison :** {{ $delivery->id }}
- **Livreur :** {{ $delivery->deliveryPerson->full_name ?? 'Non Assigné' }}
- **Heure de livraison :** {{ $delivery->delivered_at->format('d/m/Y H:i') }}

<x-mail::button :url="route('customer.orders.show', $delivery->order_id)">
Voir la commande
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>