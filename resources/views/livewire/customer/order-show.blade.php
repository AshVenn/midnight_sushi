<div>
    <x-card title="Détails de la Commande {{ $order->order_number }}" separator>
        {{-- Status --}}
        <div class="mb-4 text-sm">
            <span class="font-semibold">Statut:</span>
            <span class="uppercase">
                @switch($order->status)
                    @case('pending')
                        En attente
                        @break
                    @case('confirmed')
                        Confirmée
                        @break
                    @case('preparing')
                        En préparation
                        @break
                    @case('ready')
                        Prête
                        @break
                    @case('out_for_delivery')
                        En cours de livraison
                        @break
                    @case('delivered')
                        Livrée
                        @break
                    @case('cancelled')
                        Annulée
                        @break
                    @default
                        Inconnu
                @endswitch
            </span>
        </div>

        {{-- Additional Details --}}
        @if($order->phone_number)
            <div class="mb-4 text-sm">
                <span class="font-semibold">Téléphone:</span>
                {{ $order->phone_number }}
            </div>
        @endif

        @if($order->payment_method)
            <div class="mb-4 text-sm">
                <span class="font-semibold">Mode de paiement:</span>
                {{ $order->payment_method }}
            </div>
        @endif

        @if($order->payment_status && $order->payment_status !== 'unpaid')
            <div class="mb-4 text-sm">
                <span class="font-semibold">Statut du paiement:</span>
                {{ $order->payment_status }}
            </div>
        @endif

        {{-- Order Date --}}
        <div class="mb-4 text-sm">
            Commande passée le {{ $order->created_at->format('d/m/Y H:i') }}
        </div>

        {{-- Items --}}
        <div class="space-y-3 mb-4">
            @foreach($order->orderItems as $item)
                @php
                    $orderable = $item->orderable;
                @endphp
                <div class="p-3 border rounded flex justify-between items-center">
                    <div>
                        <strong>{{ $orderable->name ?? 'Article' }}</strong>
                        <span class="ml-2">(x{{ $item->quantity }})</span>
                    </div>
                    <div class="font-semibold">
                        {{ number_format($item->price, 2) }} CHF 
                        <span class="text-sm text-neutral ml-2">/unité</span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Notes --}}
        @if($order->notes)
            <div class="mt-4 p-3 rounded text-sm bg-base-200">
                <strong>Notes:</strong> {{ $order->notes }}
            </div>
        @endif

        {{-- Address --}}
        @if($order->address_text)
            <div class="mt-4 p-3 rounded text-sm bg-base-200">
                <strong>Adresse:</strong> {{ $order->address_text }}
            </div>
        @endif

        {{-- Total --}}
        <div class="mt-6 text-right text-lg font-bold">
            Total: {{ number_format($order->total_price, 2) }} CHF
        </div>

        {{-- Cancel Button if allowed --}}
        @if($order->can_be_cancelled)
            <div class="mt-4 text-right">
                <x-button
                    label="Annuler la commande"
                    icon="o-x-circle"
                    wire:click="cancelOrder"
                    class="btn-primary"
                />
            </div>
        @endif
    </x-card>

    {{-- TIMELINE EXAMPLE --}}
    <x-card title="Progression de la Commande" separator class="mt-6">
        <div class="px-4 py-5 space-y-4">
            @foreach ($statusTimeline as $step)
                <x-timeline-item
                    :title="$step['title']"
                    :subtitle="$step['date'] ? 'Le '.$step['date']->format('d/m/Y H:i') : ''"
                    :description="$step['description']"
                    :icon="$step['icon']"
                    :pending="!$step['is_done'] && !$step['is_current']"
                    :current="$step['is_current']"
                />
            @endforeach
        </div>
    </x-card>

    {{-- Modal for cancellation confirmation --}}
    @if($confirmingCancellation)
        <x-modal title="Confirmer l'annulation" separator wire:model="confirmingCancellation">
            <div class="space-y-4">
                <p>Êtes-vous sûr de vouloir annuler cette commande ?</p>
            </div>

            <x-slot:actions>
                <x-button label="Non" class="btn-ghost" @click="$wire.confirmingCancellation = false" />
                <x-button
                    label="Oui, annuler"
                    icon="o-check"
                    wire:click="cancelOrder"
                    class="btn-error"
                    spinner
                />
            </x-slot:actions>
        </x-modal>
    @endif
</div>