<x-card title="Order Management" separator>
    {{-- Filters Modal --}}
    <x-modal wire:model="showFiltersModal" title="Filters" right separator with-close-button>
        <div class="text-center">
            <p>Filter by delivery status!</p>
            <x-select 
                wire:model="deliveryStatusFilter" 
                :options="$deliveryStatusOptions" 
                icon="o-flag" 
                placeholder="Select Status" 
            />
        </div>
        <x-slot:actions>
            <x-button label="Close" wire:click="$set('showFiltersModal', false)" />
        </x-slot:actions>
    </x-modal>


    <div class="mb-4 sm:space-x-2 space-y-4 lg:flex lg:space-y-0 lg:items-center lg:justify-between">
        {{-- Filters Button --}}
        <div class="lg:flex-shrink-0">
            <x-button label="Filters" wire:click="$set('showFiltersModal', true)" icon="o-flag" class="w-full lg:w-auto" />
        </div>

        {{-- Search Input --}}
        <div class="lg:flex-grow">
            <x-input 
                wire:model="search" 
                placeholder="Search orders..." 
                icon="o-magnifying-glass" 
                class="w-full"
            />
        </div>
    </div>


    {{-- Table Component --}}
    <x-table :headers="$headers" :rows="$orders">
        {{-- Render Order ID --}}
        @scope('cell_id', $order)
            <div>{{ $order->id }}</div>
        @endscope

        {{-- Render Customer --}}
        @scope('cell_customer', $order)
            <div>{{ $order->customer->full_name ?? 'No Customer' }}</div>
        @endscope

        {{-- Render Delivery Address --}}
        @scope('cell_delivery_address', $order)
            <div>{{ $order->shippingAddress->full_address ?? 'No Address' }}</div>
        @endscope

        {{-- Render Total Price --}}
        @scope('cell_total_price', $order)
            <div>€{{ number_format($order->total_price, 2) }}</div>
        @endscope

        {{-- Render Status --}}
            @scope('cell_status', $order)
                <div>
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
                </div>
            @endscope


        {{-- Render Assigned To --}}
        @scope('cell_assigned_to', $order)
            <div>{{ $order->deliveryPerson->full_name ?? 'Unassigned' }}</div>
        @endscope

        {{-- Render Actions --}}
            @scope('cell_actions', $order)
                <div class="flex gap-2">
                    @if (in_array($order->status, ['delivered', 'cancelled', 'preparing']))
                        {{-- No actions for delivered, cancelled, or preparing orders --}}
                        <x-badge value="-" class="badge-error" />
                    @elseif ($order->status === 'ready' && $order->deliveryPerson)
                        {{-- Reassign Button for ready orders with an assigned delivery person --}}
                        <x-button wire:click="reassignDelivery({{ $order->id }})" class="btn-warning" label="Reassign" />
                    @elseif ($order->status === 'ready' && !$order->deliveryPerson)
                        {{-- Assign Button for ready orders without a delivery person --}}
                        <x-button wire:click="assignDelivery({{ $order->id }})" class="btn-primary" label="Assign" />
                    @else
                        <x-badge value="-" class="badge-secondary" />
                    @endif
                </div>
            @endscope


    </x-table>

    {{-- Assign Delivery Modal --}}
    <x-modal wire:model="showAssignModal" title="Assign Delivery" separator>
        <div class="text-center">
            <x-select wire:model="selectedDeliveryPerson" :options="$deliveryPeople" placeholder="Select Delivery Person" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.cancelAssign()" />
            <x-button label="Assign" class="btn-primary" wire:click="confirmAssign" />
        </x-slot:actions>
    </x-modal>

    {{-- Reassign Delivery Modal --}}
    <x-modal wire:model="showReassignModal" title="Reassign Delivery" separator>
        <div class="text-center">
            <x-select wire:model="selectedDeliveryPerson" :options="$deliveryPeople" placeholder="Select New Delivery Person" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.cancelReassign()" />
            <x-button label="Reassign" class="btn-warning" wire:click="confirmReassign" />
        </x-slot:actions>
    </x-modal>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</x-card>
