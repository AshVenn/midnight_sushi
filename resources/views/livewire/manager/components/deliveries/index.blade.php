<x-card title="Gestion des Livraisons" separator>
    {{-- Filters Modal --}}
    <x-modal wire:model="showFiltersModal" title="Filtres" right separator>
        <div class="space-y-4">
            <x-select wire:model="statusFilter" :options="$deliveryStatusOptions" placeholder="Statut" />
            <x-select wire:model="deliveryPersonFilter" :options="$deliveryPeople" placeholder="Livreur" />
            <div>
                <label class="block font-medium mb-1">Depuis:</label>
                <x-datepicker wire:model="startDate" />
            </div>
            <div>
                <label class="block font-medium mb-1">Jusqu'à:</label>
                <x-datepicker wire:model="endDate" />
            </div>
        </div>
        <x-slot:actions>
            <x-button label="Fermer" wire:click="$set('showFiltersModal', false)" />
        </x-slot:actions>
    </x-modal>

    {{-- Action Bar --}}
    <div class="mb-4 sm:space-x-2 space-y-4 lg:flex lg:space-y-0 lg:items-center lg:justify-between">

        <div class="lg:flex-shrink-0">
            <x-button label="Filtres" icon="o-funnel" wire:click="openFiltersModal" class="btn-ghost" />
        </div>
       
        <div class="lg:flex-grow">
            <x-input wire:model.live="search" icon="o-magnifying-glass" placeholder="Rechercher..." class="flex-1" />
        </div>
        
    </div>

    {{-- Deliveries Table --}}
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

        @scope('cell_delivery_person', $delivery)
            <div>{{ $delivery->deliveryPerson->full_name ?? 'Non Assigné' }}</div>
        @endscope

        @scope('cell_assigned_at', $delivery)
            <div>{{ $delivery->assigned_at ?? '-' }}</div>
        @endscope

        @scope('cell_delivered_at', $delivery)
            <div>{{ $delivery->delivered_at ?? '-' }}</div>
        @endscope

        @scope('cell_returned_at', $delivery)
            <div>{{ $delivery->returned_at ?? '-' }}</div>
        @endscope

        @scope('cell_status', $delivery)
            <x-badge 
                :value="match ($delivery->status) {
                    'assigned'  => 'Attribuée',
                    'picked_up' => 'Ramassée',
                    'delivered' => 'Livrée',
                    'returned'  => 'Retournée',
                    default     => ucfirst($delivery->status),
                }" 
                class="badge-{{ match ($delivery->status) {
                    'assigned'  => 'primary',
                    'picked_up' => 'warning',
                    'delivered' => 'success',
                    'returned'  => 'error',
                    default     => 'neutral',
                } }}"
            />
        @endscope

        @scope('cell_actions', $delivery)
            <div class="flex gap-2">
                <x-button icon="o-eye" wire:click="showDeliveryDetails({{ $delivery->id }})" class="btn-ghost btn-sm" />
                @if(!in_array($delivery->status, ['delivered', 'returned']))
                    <x-button icon="o-arrow-path" wire:click="reassignDelivery({{ $delivery->id }})" class="btn-warning btn-sm" />
                    <x-button icon="o-bolt" wire:click="performAction({{ $delivery->id }})" class="btn-primary btn-sm" />
                @endif
            </div>
        @endscope
    </x-table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $deliveries->links() }}
    </div>
    
    {{-- Reassign Modal --}}
    <x-modal wire:model="showReassignModal" title="Réassignation">
        <x-select wire:model="selectedDeliveryPerson" :options="$deliveryPeople" placeholder="Nouveau livreur" />
        <x-slot:actions>
            <x-button label="Annuler" @click="$wire.showReassignModal = false" />
            <x-button label="Confirmer" class="btn-primary" wire:click="confirmReassign" />
        </x-slot:actions>
    </x-modal>

    {{-- Action Modal --}}
    <x-modal wire:model="showActionModal" title="Action sur livraison" separator>
        <x-select 
            wire:model="selectedAction" 
            :options="$actionsOptions" 
            placeholder="Sélectionnez une action" 
        />
        <x-slot:actions>
            <x-button label="Annuler" @click="$wire.showActionModal = false" />
            <x-button label="Continuer" class="btn-primary" wire:click="requestReturnReason" />
        </x-slot:actions>
    </x-modal>

    {{-- Return Reason Modal --}}
    <x-modal wire:model="showReturnReasonModal" title="Raison du retour" separator>
        <div class="space-y-4">
            <x-select 
                wire:model.live="selectedReturnReason" 
                :options="collect($returnReasons)->map(fn($r) => ['id' => $r, 'name' => $r])" 
                placeholder="Choisir la raison"
                label="Raison du retour"
            />
            
            @if($showCustomReason)
                <div class="relative">
                    <x-textarea 
                        wire:model="customReturnReason" 
                        label="Détails supplémentaires" 
                        placeholder="Veuillez préciser la raison du retour..."
                        rows="3"
                        class="mt-2"
                    />
                    <div class="text-sm text-gray-500 mt-1 text-right">
                        {{ strlen($customReturnReason) }}/1000 caractères
                    </div>
                </div>
            @endif
            @error('customReturnReason')
                <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
            @enderror
        </div>
        
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelAction" />
            <x-button 
                label="Confirmer" 
                class="btn-primary" 
                wire:click="confirmAction" 
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-not-allowed"
            />
        </x-slot:actions>
    </x-modal>

    {{-- Delivery Details Modal --}}
    <x-modal wire:model="showDeliveryDetailsModal" title="Détails de livraison" separator>
        @if($deliveryToShow)
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-semibold">ID Livraison:</p>
                        <p>{{ $deliveryToShow->id }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">ID Commande:</p>
                        <p>{{ $deliveryToShow->order_id }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Statut:</p>
                        <x-badge 
                            :value="match ($deliveryToShow->status) {
                                'assigned'  => 'Attribuée',
                                'picked_up' => 'Ramassée',
                                'delivered' => 'Livrée',
                                'returned'  => 'Retournée',
                                default     => ucfirst($deliveryToShow->status),
                            }" 
                            class="badge-{{ match ($deliveryToShow->status) {
                                'assigned'  => 'primary',
                                'picked_up' => 'warning',
                                'delivered' => 'success',
                                'returned'  => 'error',
                                default     => 'neutral',
                            } }}"
                        />
                    </div>
                    @scope('cell_delivery_person', $delivery)
                        <p>{{ $delivery->deliveryPerson->full_name ?? 'Non assigné' }}</p>
                    @endscope
                </div>

                <div class="bg-base-200 p-4 rounded-lg">
                    <p class="font-semibold">Client:</p>
                    <p>{{ $deliveryToShow->order->customer->full_name }}</p>
                    <p class="text-sm text-gray-500">{{ $deliveryToShow->order->customer->email }}</p>
                    <p class="text-sm text-gray-500">{{ $deliveryToShow->order->customer->phone_number }}</p>
                </div>

                <div class="bg-base-200 p-4 rounded-lg">
                    <p class="font-semibold">Adresse de livraison:</p>
                    <p>{{ $deliveryToShow->order->address_text }}</p>
                </div>

                @if($deliveryToShow->status === 'returned')
                    <div class="bg-base-200 p-4 rounded-lg">
                        <p class="font-semibold text-red-600">Raison du retour:</p>
                        <p>{{ $deliveryToShow->return_reason }}</p>
                        <p class="text-sm text-red-500 mt-2">Retournée le: {{ $deliveryToShow->returned_at->format('d/m/Y H:i') }}</p>
                    </div>
                @endif

                <div class="bg-base-200 p-4 rounded-lg">
                    <p class="font-semibold">Articles:</p>
                    <ul class="list-disc list-inside">
                        @foreach($deliveryToShow->order->orderItems as $item)
                            <li>
                                {{ $item->orderable->name }} (x{{ $item->quantity }})
                                <span class="text-sm text-gray-500">- {{ number_format($item->price, 2) }} €</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-2 font-semibold">Total: {{ number_format($deliveryToShow->order->total_price, 2) }} €</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-semibold">Assignée le:</p>
                        <p>{{ $deliveryToShow->assigned_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Ramassée le:</p>
                        <p>{{ $deliveryToShow->picked_up_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Livrée le:</p>
                        <p>{{ $deliveryToShow->delivered_at?->format('d/m/Y H:i') ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Frais de livraison:</p>
                        <p>{{ number_format($deliveryToShow->delivery_fee, 2) }} €</p>
                    </div>
                </div>
            </div>
        @endif
        <x-slot:actions>
            <x-button label="Fermer" @click="$wire.showDeliveryDetailsModal = false" />
        </x-slot:actions>
    </x-modal>
</x-card>
