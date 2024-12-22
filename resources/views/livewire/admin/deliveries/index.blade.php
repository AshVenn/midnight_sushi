<x-card title="Gestion des Livraisons" separator>
    {{-- Modal des Filtres --}}
    <x-modal wire:model="showFiltersModal" title="Filtres" right separator with-close-button>
        <div class="space-y-4">
            <p class="text-center font-semibold">Filtrer par :</p>
            
            {{-- Filtre par Statut --}}
            <x-select 
                wire:model="statusFilter" 
                :options="$deliveryStatusOptions" 
                placeholder="Sélectionnez un Statut" 
                label="Statut"
            />

            {{-- Filtre par Livreur --}}
            <x-select 
                wire:model="deliveryPersonFilter" 
                :options="$deliveryPeople" 
                placeholder="Sélectionnez un Livreur" 
                label="Livreur"
            />

            {{-- Filtre par Période --}}
            @php
                $dateConfig = [
                    'altFormat'  => 'j F, Y',
                    'dateFormat' => 'Y-m-d',
                ];
            @endphp

            <div>
                <label class="block font-medium mb-1">Assigné Depuis :</label>
                <x-datepicker 
                    wire:model="startDate" 
                    :config="$dateConfig"
                    placeholder="Date Début" 
                    icon="o-calendar"
                />
            </div>

            <div>
                <label class="block font-medium mb-1">Assigné Jusqu'à :</label>
                <x-datepicker 
                    wire:model="endDate" 
                    :config="$dateConfig"
                    placeholder="Date Fin" 
                    icon="o-calendar"
                />
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Fermer" wire:click="$set('showFiltersModal', false)" />
        </x-slot:actions>
    </x-modal>


    <div class="mb-4 sm:space-x-2 space-y-4 lg:flex lg:space-y-0 lg:items-center lg:justify-between">
        {{-- Bouton Filtres --}}
        <div class="lg:flex-shrink-0">
            <x-button label="Filtres" wire:click="openFiltersModal" icon="o-flag" class="w-full lg:w-auto" />
        </div>

        {{-- Champ de Recherche --}}
        <div class="lg:flex-grow">
            <x-input 
                wire:model.lazy="search" 
                wire:enter="search" 
                placeholder="Rechercher par ID Livraison, ID Commande ou Client..." 
                icon="o-magnifying-glass" 
                class="w-full"
            />
        </div>
    </div>

    <x-table :headers="$headers" :rows="$deliveries" :sort-by="$sortBy">
        {{-- Contenu de la Table --}}
        @scope('cell_id', $delivery)
            <div>{{ $delivery->id }}</div>
        @endscope

        @scope('cell_order_id', $delivery)
            <div>{{ $delivery->order_id }}</div>
        @endscope

        @scope('cell_customer', $delivery)
            <div>{{ $delivery->order->customer->full_name ?? 'N/A' }}</div>
        @endscope

        @scope('cell_delivery_person', $delivery)
            <div>{{ $delivery->deliveryPerson->full_name ?? 'Non Assigné' }}</div>
        @endscope

        @scope('cell_status', $delivery)
            @php
                $statusLabels = [
                    'assigned'  => 'Attribuée',
                    'picked_up' => 'Ramassée',
                    'delivered' => 'Livrée',
                ];
                $badgeColors = [
                    'assigned'  => 'bg-blue-500 text-white',
                    'picked_up' => 'bg-yellow-500 text-white',
                    'delivered' => 'bg-green-500 text-white',
                    'default'   => 'bg-gray-500 text-white',
                ];
                $translatedStatus = $statusLabels[$delivery->status] ?? ucfirst($delivery->status);
                $badgeClass       = $badgeColors[$delivery->status] ?? $badgeColors['default'];
            @endphp
            <x-badge :value="$translatedStatus" class="{{ $badgeClass }}" />
        @endscope

        @scope('cell_assigned_at', $delivery)
            <div>{{ $delivery->assigned_at ? $delivery->assigned_at->format('d/m/Y H:i') : 'N/A' }}</div>
        @endscope

        @scope('cell_delivered_at', $delivery)
            <div>{{ $delivery->delivered_at ? $delivery->delivered_at->format('d/m/Y H:i') : '-' }}</div>
        @endscope

        @scope('cell_actions', $delivery)
            <div class="flex gap-2">
                {{-- Eye button to see details --}}
                <x-button 
                    icon="o-eye" 
                    wire:click="showDeliveryDetails({{ $delivery->id }})" 
                    class="btn-secondary" 
                    label="Voir" 
                />

                @if ($delivery->status === 'delivered')
                    <x-badge value="-" class="badge-secondary" />
                @else
                    <x-button 
                        wire:click="reassignDelivery({{ $delivery->id }})" 
                        class="btn-warning" 
                        label="Réassigner" 
                    />
                    @if ($delivery->status === 'assigned' || $delivery->status === 'picked_up')
                        <x-button 
                            wire:click="performAction({{ $delivery->id }})" 
                            class="btn-primary" 
                            label="Actions" 
                        />
                    @endif
                @endif
            </div>
        @endscope
    </x-table>

    {{-- Modal de Réassignation --}}
    <x-modal wire:model="showReassignModal" title="Réassigner une Livraison" separator>
        <div class="text-center">
            <x-select wire:model="selectedDeliveryPerson" 
                      :options="$deliveryPeople" 
                      placeholder="Sélectionnez un Livreur" />
        </div>

        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelReassign" />
            <x-button label="Réassigner" class="btn-warning" wire:click="confirmReassign" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal Actions --}}
    <x-modal wire:model="showActionModal" title="Effectuer une Action" separator>
        <div class="text-center">
            <x-select 
                wire:model="selectedAction" 
                :options="$actionsOptions" 
                placeholder="Sélectionnez une Action" 
            />
        </div>

        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelAction" />
            <x-button label="Confirmer" class="btn-primary" wire:click="confirmAction" />
        </x-slot:actions>
    </x-modal>

    {{-- NEW: Modal to show Delivery/Order details --}}
    <x-modal wire:model="showDeliveryDetailsModal" title="Détails de la Livraison" separator>
        @if($deliveryToShow)
            <div class="space-y-4 p-2">
                <h3 class="font-bold text-lg">
                    Livraison #{{ $deliveryToShow->id }} pour la Commande #{{ $deliveryToShow->order_id }}
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

                <p><strong>Livreur:</strong> {{ $deliveryToShow->deliveryPerson->full_name ?? 'Non Assigné' }}</p>

                <hr />

                <h4 class="font-semibold">Détails de la Commande</h4>
                <p><strong>Client:</strong> {{ $deliveryToShow->order->customer->full_name ?? 'N/A' }}</p>
                <p><strong>Téléphone:</strong> {{ $deliveryToShow->order->phone_number }}</p>
                <p><strong>Adresse:</strong> {{ $deliveryToShow->order->address_text }}</p>
                <p><strong>Notes:</strong> {{ $deliveryToShow->order->notes }}</p>
                <p><strong>Prix Total:</strong> {{ number_format($deliveryToShow->order->total_price, 2) }} €</p>

                <hr />

                <h4 class="font-semibold">Articles de la Commande:</h4>
                <ul class="list-disc list-inside">
                    @forelse($deliveryToShow->order->orderItems as $item)
                        @php
                            // Each $item->orderable might be a Product or Menu
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
            </div>
        @endif

        <x-slot:actions>
            <x-button label="Fermer" wire:click="closeDeliveryDetailsModal" />
        </x-slot:actions>
    </x-modal>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $deliveries->links() }}
    </div>
</x-card>
