<div>
    <x-card title="Mes Livraisons Passées" separator>
        {{-- Filtre et Recherche --}}
        <div class="mb-4 flex flex-wrap gap-4 lg:justify-between">
            {{-- Champ de Recherche --}}
            <x-input
                wire:model.lazy="search"
                placeholder="Rechercher par ID ou client"
                icon="o-magnifying-glass"
                class="flex-grow w-full"
                autocomplete="off"
            />

            {{-- Filtres de Date --}}
            @php
                $dateConfig = [
                    'altFormat'  => 'j F, Y',
                    'dateFormat' => 'Y-m-d',
                ];
            @endphp

            <x-datepicker
                wire:model="startDate"
                :config="$dateConfig"
                placeholder="Date Début"
                icon="o-calendar"
                class="flex-grow lg:flex-none w-full"
            />

            <x-datepicker
                wire:model="endDate"
                :config="$dateConfig"
                placeholder="Date Fin"
                icon="o-calendar"
                class="flex-grow lg:flex-none w-full"
            />
        </div>

        {{-- Table des Livraisons --}}
        <x-table :headers="$headers" :rows="$deliveries" :sort-by="$sortBy">
            @scope('cell_id', $delivery)
                <div>{{ $delivery->id }}</div>
            @endscope

            @scope('cell_order_id', $delivery)
                <div>{{ $delivery->order_id }}</div>
            @endscope

            @scope('cell_customer', $delivery)
                <div>{{ $delivery->order->customer->full_name ?? 'N/A' }}</div>
            @endscope

            @scope('cell_address_text', $delivery)
                <div>{{ $delivery->address_text ?? 'Adresse non disponible' }}</div>
            @endscope

            @scope('cell_delivered_at', $delivery)
                <div>
                    {{ $delivery->delivered_at
                        ? $delivery->delivered_at->format('d/m/Y H:i')
                        : '-' }}
                </div>
            @endscope

            {{-- Actions Column: Show Delivery Details --}}
            @scope('cell_actions', $delivery)
                <div class="flex gap-2">
                    <x-button
                        icon="o-eye"
                        wire:click="showDeliveryDetails({{ $delivery->id }})"
                        class="btn-secondary"
                        label="Voir"
                    />
                </div>
            @endscope
        </x-table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $deliveries->links() }}
        </div>
    </x-card>

    {{-- Modal for Delivery Details --}}
    <x-modal wire:model="showDeliveryDetailsModal" title="Détails de la Livraison" separator>
        @if($deliveryToShow)
            <div class="space-y-4 p-2">
                <h3 class="font-bold text-lg">
                    Livraison #{{ $deliveryToShow->id }}
                    (Commande #{{ $deliveryToShow->order_id }})
                </h3>

                <p>
                    <strong>Statut:</strong>
                    @php
                        $status = $deliveryToShow->status;
                        $label  = match($status) {
                            'assigned'  => 'Attribuée',
                            'picked_up' => 'Ramassée',
                            'delivered' => 'Livrée',
                            default     => $status
                        };
                    @endphp
                    {{ $label }}
                </p>

                <hr />

                <h4 class="font-semibold">Détails du Client</h4>
                <p>
                    <strong>Nom:</strong>
                    {{ $deliveryToShow->order->customer->full_name ?? 'N/A' }}
                </p>
                <p>
                    <strong>Téléphone:</strong>
                    {{ $deliveryToShow->order->phone_number }}
                </p>
                <p>
                    <strong>Adresse:</strong>
                    {{ $deliveryToShow->order->address_text }}
                </p>
                <p>
                    <strong>Notes:</strong>
                    {{ $deliveryToShow->order->notes }}
                </p>

                <hr />

                <h4 class="font-semibold">Articles de la Commande:</h4>
                <ul class="list-disc list-inside">
                    @forelse($deliveryToShow->order->orderItems as $item)
                        @php
                            $itemName = method_exists($item->orderable, 'name')
                                ? $item->orderable->name
                                : ($item->orderable->name ?? 'Item');
                            $itemPrice = number_format($item->price, 2);
                        @endphp
                        <li>
                            {{ $itemName }} (x{{ $item->quantity }})
                            - {{ $itemPrice }} €
                        </li>
                    @empty
                        <li>Aucun article.</li>
                    @endforelse
                </ul>

                <hr />

                <p>
                    <strong>Prix Total:</strong>
                    {{ number_format($deliveryToShow->order->total_price, 2) }} €
                </p>
            </div>
        @endif

        <x-slot:actions>
            <x-button
                label="Fermer"
                wire:click="closeDeliveryDetailsModal"
            />
        </x-slot:actions>
    </x-modal>
</div>
