<div>
    <x-card title="Mes Livraisons" separator>
        {{-- Filtre et Recherche --}}
        <div class="mb-4 flex flex-wrap gap-4 lg:justify-between">
            <x-select
                wire:model="statusFilter"
                :options="[
                    ['id' => 'all', 'name' => 'Tous les Statuts'],
                    ['id' => 'assigned', 'name' => 'Attribuée'],
                    ['id' => 'picked_up', 'name' => 'Ramassée'],
                ]"
                placeholder="Filtrer par Statut"
                label="Statut"
                class="flex-grow lg:flex-none"
                wire:change="updateStatusFilter"
            />

            <x-input
                wire:model.lazy="search"
                placeholder="Rechercher par ID ou client"
                icon="o-magnifying-glass"
                class="flex-grow"
                autocomplete="off"
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
                <div>{{ $delivery->address_text ?? 'N/A' }}</div>
            @endscope

            @scope('cell_status', $delivery)
                <x-badge 
                    :value="match ($delivery->status) {
                        'assigned'  => 'Attribuée',
                        'picked_up' => 'Ramassée',
                        default     => ucfirst($delivery->status),
                    }" 
                    class="{{ match ($delivery->status) {
                        'assigned'  => 'badge-primary',
                        'picked_up' => 'badge-warning',
                        default     => 'badge-secondary',
                    } }}" 
                />
            @endscope

            @scope('cell_assigned_at', $delivery)
                <div>{{ $delivery->assigned_at ? $delivery->assigned_at->format('d/m/Y H:i') : 'N/A' }}</div>
            @endscope

            @scope('cell_actions', $delivery)
                <div class="flex gap-2">
                    {{-- NEW: Button to show details --}}
                    <x-button 
                        icon="o-eye" 
                        wire:click="showDeliveryDetails({{ $delivery->id }})" 
                        class="btn-secondary" 
                        label="Voir"
                    />

                    @if ($delivery->status === 'assigned')
                        <x-button
                            wire:click="markAsPickedUp({{ $delivery->id }})"
                            class="btn-secondary"
                            label="Ramassée"
                        />
                    @elseif ($delivery->status === 'picked_up')
                        <x-button
                            wire:click="markAsDelivered({{ $delivery->id }})"
                            class="btn-primary"
                            label="Livrée"
                        />
                    @else
                        <x-badge value="-" class="badge-secondary" />
                    @endif
                </div>
            @endscope
        </x-table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $deliveries->links() }}
        </div>
    </x-card>

    {{-- NEW: Modal for Delivery Details --}}
    <x-modal wire:model="showDeliveryDetailsModal" title="Détails de la Livraison" separator wire:key="showDeliveriesDetailsModal">
        @if($deliveryToShow)
            <div class="space-y-4 p-2">
                <h3 class="font-bold text-lg">
                    Livraison #{{ $deliveryToShow->id }} (Commande #{{ $deliveryToShow->order_id }})
                </h3>

                <p>
                    <strong>Statut: </strong>
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
                <p><strong>Nom:</strong> {{ $deliveryToShow->order->customer->full_name ?? 'N/A' }}</p>
                <p><strong>Téléphone:</strong> {{ $deliveryToShow->order->phone_number }}</p>
                <p><strong>Adresse:</strong> {{ $deliveryToShow->order->address_text }}</p>
                <p><strong>Notes:</strong> {{ $deliveryToShow->order->notes }}</p>

                <hr />

                <h4 class="font-semibold">Articles de la Commande:</h4>
                <ul class="list-disc list-inside">
                    @forelse($deliveryToShow->order->orderItems as $item)
                        @php
                            $itemName  = method_exists($item->orderable, 'name') 
                                ? $item->orderable->name 
                                : ($item->orderable->name ?? 'Item');
                            $itemPrice = number_format($item->price, 2);
                        @endphp
                        <li>
                            {{ $itemName }} (x{{ $item->quantity }}) - {{ $itemPrice }} €
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
            <x-button label="Fermer" wire:click="closeDeliveryDetailsModal" />
        </x-slot:actions>
    </x-modal>
</div>
