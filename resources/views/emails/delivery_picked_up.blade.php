<x-mail::message>
# Votre commande a été ramassée

Bonjour {{ $delivery->order->customer->first_name }},

Votre commande **{{ $delivery->order->order_number }}** a été ramassée par notre livreur et est en route pour la livraison.

**Détails de la livraison :**
- **Numéro de livraison :** {{ $delivery->id }}
- **Livreur :** {{ $delivery->deliveryPerson->full_name ?? 'Non Assigné' }}
- **Heure de ramassage :** {{ $delivery->picked_up_at->format('d/m/Y H:i') }}

<x-mail::button :url="route('customer.orders.show', $delivery->order_id)">
Voir la commande
</x-mail::button>

Merci,<br>
{{ config('app.name') }}
</x-mail::message>