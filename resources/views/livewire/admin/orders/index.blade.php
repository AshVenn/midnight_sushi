<x-card title="Gestion des Commandes" separator>
    {{-- Modal des Filtres --}}
    <x-modal wire:model="showFiltersModal" title="Filtres" wire:key="orders-filters-modal" right separator with-close-button>
        <div class="space-y-4">
            <p class="text-center font-semibold">Filtrer par divers critères :</p>

            {{-- Filtre par Statut --}}
            <x-select 
                wire:model="deliveryStatusFilter" 
                :options="$deliveryStatusOptions" 
                icon="o-flag" 
                placeholder="Sélectionnez un Statut" 
                label="Statut de Livraison"
            />

            {{-- Filtre par Livreur --}}
            <x-select 
                wire:model="deliveryPersonFilter"
                :options="$deliveryPeople"
                placeholder="Sélectionnez un Livreur"
                label="Livreur Assigné"
            />

            {{-- Filtre par Période --}}
            @php
                $dateConfig = [
                    'altFormat' => 'F j, Y',
                    'dateFormat' => 'Y-m-d',
                ];
            @endphp

            <div>
                <label class="block font-medium mb-1">Créé Depuis :</label>
                <x-datepicker 
                    wire:model="startDate" 
                    :config="$dateConfig"
                    placeholder="Date Début" 
                    icon="o-calendar"
                />
            </div>

            <div>
                <label class="block font-medium mb-1">Créé Jusqu'à :</label>
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
                placeholder="Rechercher par ID de commande ou nom complet du client..." 
                icon="o-magnifying-glass" 
                autocomplete="off" 
                wire:enter="search"
            />
        </div>
    </div>

    <x-table :headers="$headers" :rows="$orders" :sort-by="$sortBy">
        @scope('cell_id', $order)
            <div>{{ $order->id }}</div>
        @endscope
        
        @scope('cell_order_number', $order)
            <div>{{ $order->order_number }}</div>
        @endscope

        @scope('cell_customer', $order)
            <div>{{ $order->customer->full_name ?? 'Pas de Client' }}</div>
        @endscope

        @scope('cell_address_text', $order)
            <div>{{ $order->address_text ?? 'Adresse Manquante' }}</div>
        @endscope

        @scope('cell_total_price', $order)
            <div>{{ number_format($order->total_price, 2) }} CHF</div>
        @endscope

        @scope('cell_status', $order)
        <x-badge :value="$order->status_text" :class="$order->status_color" />
        @endscope

        @scope('cell_created_at', $order)
            <div>{{ $order->created_at->format('d/m/Y H:i') }}</div>
        @endscope

        @scope('cell_assigned_to', $order)
            <div>{{ $order->currentDelivery()?->deliveryPerson->full_name ?? 'Non Assigné' }}</div>
        @endscope

        @scope('cell_actions', $order)
            <div class="flex gap-2 items-center">
                {{-- Eye Button to view details --}}
                <x-button icon="o-eye" wire:click="viewOrderDetails({{ $order->id }})" />

                @if (in_array($order->status, ['delivered', 'cancelled']))
                    <x-badge value="-" class="badge-error" />
                @elseif ($order->status === 'preparing')
                    <x-button wire:click="markAsReady({{ $order->id }})" class="btn-secondary" label="Prête !" />
                @elseif ($order->status === 'ready' && $order->currentDelivery()?->deliveryPerson)
                    <x-button wire:click="reassignDelivery({{ $order->id }})" class="btn-warning" label="Réassigner" />
                @elseif ($order->status === 'ready' && !$order->currentDelivery()?->deliveryPerson)
                    <x-button wire:click="assignDelivery({{ $order->id }})" class="btn-primary" label="Assigner" />
                @else
                    <x-badge value="-" class="badge-secondary" />
                @endif
            </div>
        @endscope
    </x-table>

    {{-- Modal d'Assignation de Livraison --}}
    <x-modal wire:model="showAssignModal" title="Assigner une Livraison" separator>
        <div class="text-center">
            <x-select 
                wire:model="selectedDeliveryPerson" 
                :options="$deliveryPeople" 
                placeholder="Sélectionnez un Livreur"
            />
        </div>
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelAssign" />
            <x-button label="Assigner" class="btn-primary" wire:click="confirmAssign" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal de Réassignation de Livraison --}}
    <x-modal wire:model="showReassignModal" title="Réassigner une Livraison" separator>
        <div class="text-center">
            <x-select 
                wire:model="selectedDeliveryPerson" 
                :options="$deliveryPeople" 
                placeholder="Sélectionnez un Nouveau Livreur"
            />
        </div>
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelReassign" />
            <x-button label="Réassigner" class="btn-warning" wire:click="confirmReassign" />
        </x-slot:actions>
    </x-modal>

    {{-- Modal for Viewing Order Details --}}
    <x-modal wire:model="showOrderDetailsModal" title="Détails de la Commande" separator>
        @if($orderDetails)
            <div class="space-y-4">
                <div>
                    <strong>Commande #:</strong> {{ $orderDetails->id }}<br>
                    <strong>Client:</strong> {{ $orderDetails->customer->full_name ?? 'N/A' }}<br>
                    <strong>Téléphone:</strong> {{ $orderDetails->phone_number ?? 'N/A' }}<br>
                    <strong>Adresse:</strong> {{ $orderDetails->address_text ?? 'N/A' }}<br>
                    <strong>Notes:</strong> {{ $orderDetails->notes ?? 'N/A' }}<br>
                    <strong>Statut:</strong> {{ $orderDetails->status }}<br>
                    <strong>Date de Création:</strong> {{ $orderDetails->created_at->format('d/m/Y H:i') }}
                </div>

                <hr>

                <div>
                    <strong>Articles commandés:</strong>
                    <table class="min-w-full divide-y divide-gray-200 mt-2">
                        <thead>
                            <tr>
                                <th class="px-4 py-2">Article</th>
                                <th class="px-4 py-2">Quantité</th>
                                <th class="px-4 py-2">Prix Unitaire</th>
                                <th class="px-4 py-2">Sous-total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orderDetails->orderItems as $item)
                                @php
                                    // If using direct references: $item->product->name
                                    // If using polymorphic: $item->orderable->name
                                    $name  = method_exists($item->orderable, 'getAttribute') 
                                              ? $item->orderable->name 
                                              : 'N/A';
                                    $price = $item->price;
                                    $qty   = $item->quantity;
                                @endphp
                                <tr>
                                    <td class="px-4 py-2">{{ $name }}</td>
                                    <td class="px-4 py-2 text-center">{{ $qty }}</td>
                                    <td class="px-4 py-2">{{ number_format($price, 2) }} CHF</td>
                                    <td class="px-4 py-2">
                                        {{ number_format($price * $qty, 2) }} CHF
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <hr>

                <div>
                    <strong>Total:</strong> {{ number_format($orderDetails->total_price, 2) }} CHF
                </div>
            </div>
        @endif
        <x-slot:actions>
            <x-button label="Fermer" wire:click="$set('showOrderDetailsModal', false)" />
        </x-slot:actions>
    </x-modal>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</x-card>
