<x-card title="Gestion des Commandes" separator>
    {{-- Filters Modal --}}
    <x-modal wire:model="showFiltersModal" title="Filtres" right separator with-close-button>
        <div class="space-y-4">
            <p class="text-center font-semibold">Filtrer par divers critères :</p>

            {{-- Status Filter --}}
            <x-select 
                wire:model="deliveryStatusFilter" 
                :options="$deliveryStatusOptions" 
                icon="o-flag" 
                placeholder="Sélectionnez un Statut" 
                label="Statut de Livraison"
            />

            {{-- Delivery Person Filter --}}
            <x-select 
                wire:model="deliveryPersonFilter"
                :options="$deliveryPeople"
                placeholder="Sélectionnez un Livreur"
                label="Livreur Assigné"
            />

            {{-- Date Range Filter --}}
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
        {{-- Filters Button --}}
        <div class="lg:flex-shrink-0">
            <x-button label="Filtres" wire:click="openFiltersModal" icon="o-flag" class="w-full lg:w-auto" />
        </div>

        {{-- Search Input --}}
        <div class="lg:flex-grow">
            <x-input wire:model.lazy="search" placeholder="Search by order ID or customer full name..." icon="o-magnifying-glass" autocomplete="off" wire:enter="search" />
        </div>
    </div>

    <x-table :headers="$headers" :rows="$orders" :sort-by="$sortBy">
        @scope('cell_id', $order)
            <div>{{ $order->id }}</div>
        @endscope

        @scope('cell_customer', $order)
            <div>{{ $order->customer->full_name ?? 'Pas de Client' }}</div>
        @endscope

        @scope('cell_delivery_address', $order)
            <div>{{ $order->address_text ?? 'Adresse Manquante' }}</div>
        @endscope

        @scope('cell_total_price', $order)
            <div>{{ number_format($order->total_price, 2) }} €</div>
        @endscope

        @scope('cell_created_at', $order)
            <div>{{ $order->created_at->format('d/m/Y H:i') }}</div>
        @endscope

        @scope('cell_status', $order)
            @php
                $statusClass = match($order->status) {
                    'pending' => 'badge-warning',
                    'confirmed' => 'badge-primary',
                    'preparing' => 'badge-info',
                    'out_for_delivery' => 'badge-primary',
                    'delivered' => 'badge-success',
                    'cancelled' => 'badge-error',
                    default => 'badge-secondary',
                };
            @endphp
            <x-badge :value="ucfirst($order->status)" :class="$statusClass" />
        @endscope

        @scope('cell_assigned_to', $order)
            <div>{{ $order->currentDelivery()?->deliveryPerson->full_name ?? 'Non Assigné' }}</div>
        @endscope

        @scope('cell_actions', $order)
            <div class="flex gap-2">
                @if (in_array($order->status, ['delivered', 'cancelled']))
                    <x-badge value="-" class="badge-error" />
                @elseif ($order->status === 'preparing')
                    <x-button wire:click="markAsReady({{ $order->id }})" class="btn-secondary" label="Prêt !" />
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

    {{-- Assign Delivery Modal --}}
    <x-modal wire:model="showAssignModal" title="Assigner une Livraison" separator>
        <div class="text-center">
            <x-select wire:model="selectedDeliveryPerson" :options="$deliveryPeople" placeholder="Sélectionnez un Livreur" />
        </div>
        <x-slot:actions>
            <x-button label="Annuler" @click="$wire.cancelAssign()" />
            <x-button label="Assigner" class="btn-primary" wire:click="confirmAssign" />
        </x-slot:actions>
    </x-modal>

    {{-- Reassign Delivery Modal --}}
    <x-modal wire:model="showReassignModal" title="Réassigner une Livraison" separator>
        <div class="text-center">
            <x-select wire:model="selectedDeliveryPerson" :options="$deliveryPeople" placeholder="Sélectionnez un Nouveau Livreur" />
        </div>
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelReassign" />
            <x-button label="Réassigner" class="btn-warning" wire:click="confirmReassign" />
        </x-slot:actions>
    </x-modal>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</x-card>
