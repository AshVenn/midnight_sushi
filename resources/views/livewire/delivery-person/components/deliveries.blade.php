<div>
    <x-card title="Mes Livraisons" separator>
        <div class="mb-4 flex flex-wrap gap-4 items-end">
            <div class="flex-grow">
                <x-input wire:model.lazy="search" placeholder="Rechercher..." icon="o-magnifying-glass" />
            </div>
            
            <div class="flex gap-4 items-center">
                <x-select wire:model="statusFilter" :options="[
                    ['id' => 'all', 'name' => 'Tous'],
                    ['id' => 'assigned', 'name' => 'Attribuée'],
                    ['id' => 'picked_up', 'name' => 'Ramassée'],
                    ['id' => 'delivered', 'name' => 'Livrée'],
                    ['id' => 'returned', 'name' => 'Retournée'],
                ]" label="Statut" class="w-40" wire:change="updateStatusFilter" />

                <div class="flex gap-2 items-center">
                    <x-datepicker 
                        wire:model="startDate" 
                        label="De" 
                        icon="o-calendar"
                        class="w-40"
                        wire:change="updateStatusFilter"
                    />
                    <x-datepicker 
                        wire:model="endDate" 
                        label="À" 
                        icon="o-calendar"
                        class="w-40"
                        wire:change="updateStatusFilter"
                    />
                </div>
            </div>
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
                @php
                    $status = $delivery->status;
                    $label  = match($status) {
                        'assigned'  => 'Attribuée',
                        'picked_up' => 'Ramassée',
                        'delivered' => 'Livrée',
                        'returned'  => 'Retournée',
                        default     => ucfirst($status),
                    };
                    $badgeClass = match($status) {
                        'assigned'  => 'badge-primary',
                        'picked_up' => 'badge-warning',
                        'delivered' => 'badge-success',
                        'returned'  => 'badge-error',
                        default     => 'badge-neutral',
                    };
                @endphp

                <x-badge :value="$label" class="{{ $badgeClass }}" />
            @endscope

            @scope('cell_assigned_at', $delivery)
                <div>{{ $delivery->assigned_at ? $delivery->assigned_at->format('d/m/Y H:i') : 'N/A' }}</div>
            @endscope

            @scope('cell_actions', $delivery)
                <div class="flex gap-2">
                    {{-- Details button (modal) --}}
                    <x-button
                        icon="o-eye"
                        wire:click="showDeliveryDetails({{ $delivery->id }})"
                        class="btn-ghost btn-sm"
                        label="Détails"
                    />

                    {{-- If not yet delivered/returned, show "Actions" button --}}
                    @if (!in_array($delivery->status, ['delivered', 'returned']))
                        <x-button
                            icon="o-bolt"
                            wire:click="performAction({{ $delivery->id }})"
                            class="btn-primary btn-sm"
                            label="Actions"
                        />
                    @endif
                </div>
            @endscope
        </x-table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $deliveries->links() }}
        </div>
    </x-card>

    {{-- ACTION MODAL (mark as picked up, delivered, returned) --}}
    <x-modal wire:model="showActionModal" title="Action sur la Livraison" separator>
        <x-select 
            wire:model="selectedAction" 
            :options="$actionsOptions" 
            placeholder="Choisir une action" 
        />
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelAction" />
            <x-button label="Continuer" class="btn-primary" wire:click="requestReturnReason" />
        </x-slot:actions>
    </x-modal>

    {{-- Return Reason Modal (updated layout) --}}
    {{-- Return Reason Modal --}}
        <x-modal wire:model="showReturnReasonModal" title="Raison du retour" separator>
            <div class="grid grid-cols-1 gap-4">
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


    {{-- DELIVERY DETAILS MODAL --}}
    <x-modal wire:model="showDeliveryDetailsModal" title="Détails de la Livraison" separator>
        @if($deliveryToShow)
            <div class="space-y-4 p-2">
                <h3 class="font-bold text-lg">
                    Livraison #{{ $deliveryToShow->id }} (Commande #{{ $deliveryToShow->order_id }})
                </h3>

                @php
                    $status = $deliveryToShow->status;
                    $label  = match($status) {
                        'assigned'  => 'Attribuée',
                        'picked_up' => 'Ramassée',
                        'delivered' => 'Livrée',
                        'returned'  => 'Retournée',
                        default     => ucfirst($status),
                    };
                @endphp
                <p><strong>Statut: </strong>{{ $label }}</p>

                {{-- Client details --}}
                <hr />
                <h4 class="font-semibold">Détails du Client</h4>
                <p><strong>Nom:</strong> {{ $deliveryToShow->order->customer->full_name ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $deliveryToShow->order->customer->email ?? 'N/A' }}</p>
                <p><strong>Téléphone:</strong> {{ $deliveryToShow->order->phone_number ?? 'N/A' }}</p>
                <p><strong>Adresse:</strong> {{ $deliveryToShow->order->address_text ?? 'N/A' }}</p>

                {{-- Order items --}}
                <hr />
                <h4 class="font-semibold">Articles de la Commande:</h4>
                <ul class="list-disc list-inside">
                    @foreach($deliveryToShow->order->orderItems as $item)
                        <li>
                            {{ $item->orderable->name ?? 'Article' }} (x{{ $item->quantity }})
                            - {{ number_format($item->price, 2) }} CHF
                        </li>
                    @endforeach
                </ul>
                <p class="mt-2 font-semibold">
                    Total: {{ number_format($deliveryToShow->order->total_price, 2) }} CHF
                </p>

                {{-- Return reason if status is returned --}}
                @if($deliveryToShow->status === 'returned')
                    <hr />
                    <h4 class="font-semibold text-red-600">Raison du Retour:</h4>
                    <p>{{ $deliveryToShow->return_reason }}</p>
                    <p class="text-sm text-gray-500">
                        Retournée le: 
                        {{ $deliveryToShow->returned_at ? $deliveryToShow->returned_at->format('d/m/Y H:i') : 'N/A' }}
                    </p>
                @endif

                {{-- Timeline --}}
                <hr />
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
                        <p class="font-semibold">Frais de Livraison:</p>
                        <p>{{ number_format($deliveryToShow->delivery_fee, 2) }} CHF</p>
                    </div>
                </div>
            </div>
        @endif

        <x-slot:actions>
            <x-button label="Fermer" wire:click="closeDeliveryDetailsModal" />
        </x-slot:actions>
    </x-modal>
</div>
