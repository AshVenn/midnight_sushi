<x-card title="Gestion des Livraisons" separator>
    {{-- Filters Modal --}}
    <x-modal wire:model="showFiltersModal" title="Filtres" right separator with-close-button>
        <div class="space-y-4">
            <p class="text-center font-semibold">Filtrer par :</p>
            
            {{-- Status Filter --}}
            <x-select 
                wire:model="statusFilter" 
                :options="$deliveryStatusOptions" 
                placeholder="Sélectionnez un Statut" 
                label="Statut"
            />

            {{-- Delivery Person Filter --}}
            <x-select 
                wire:model="deliveryPersonFilter" 
                :options="$deliveryPeople" 
                placeholder="Sélectionnez un Livreur" 
                label="Livreur"
            />

            {{-- Date Range Filter --}}
            @php
                $dateConfig = [
                    'altFormat' => 'j F, Y',
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
        {{-- Filters Button --}}
        <div class="lg:flex-shrink-0">
            <x-button label="Filtres" wire:click="openFiltersModal" icon="o-flag" class="w-full lg:w-auto" />
        </div>

        {{-- Search Input --}}
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
        {{-- Table Content --}}
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
                $translatedStatus = match ($delivery->status) {
                    'assigned' => 'Attribuée',
                    'picked_up' => 'Ramassée',
                    'delivered' => 'Livrée',
                    default => ucfirst($delivery->status),
                };
            @endphp
            <x-badge :value="$translatedStatus" />
        @endscope

        @scope('cell_assigned_at', $delivery)
            <div>{{ $delivery->assigned_at ? $delivery->assigned_at->format('d/m/Y H:i') : 'N/A' }}</div>
        @endscope

        @scope('cell_delivered_at', $delivery)
            <div>{{ $delivery->delivered_at ? $delivery->delivered_at->format('d/m/Y H:i') : '-' }}</div>
        @endscope

        @scope('cell_actions', $delivery)
            <div class="flex gap-2">
                @if ($delivery->status === 'delivered')
                    <x-badge value="-" class="badge-secondary" />
                @else
                    <x-button wire:click="reassignDelivery({{ $delivery->id }})" class="btn-warning" label="Réassigner" />
                    @if ($delivery->status === 'assigned' || $delivery->status === 'picked_up')
                        <x-button wire:click="performAction({{ $delivery->id }})" class="btn-primary" label="Actions" />
                    @endif
                @endif
            </div>
        @endscope
    </x-table>

    {{-- Reassign Modal --}}
    <x-modal wire:model="showReassignModal" title="Réassigner une Livraison" separator>
        <div class="text-center">
            <x-select wire:model="selectedDeliveryPerson" :options="$deliveryPeople" placeholder="Sélectionnez un Livreur" />
        </div>

        <x-slot:actions>
            <x-button label="Annuler" @click="$wire.cancelReassign()" />
            <x-button label="Réassigner" class="btn-warning" wire:click="confirmReassign" />
        </x-slot:actions>
    </x-modal>

    {{-- Actions Modal --}}
    <x-modal wire:model="showActionModal" title="Effectuer une Action" separator>
        <div class="text-center">
            <x-select wire:model="selectedAction" :options="$actionsOptions" placeholder="Sélectionnez une Action" />
        </div>

        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelAction" />
            <x-button label="Confirmer" class="btn-primary" wire:click="confirmAction" />
        </x-slot:actions>
    </x-modal>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $deliveries->links() }}
    </div>
</x-card>
