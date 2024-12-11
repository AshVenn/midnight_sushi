<x-card title="Order Management" separator>
    {{-- Filters Modal --}}
    <x-modal wire:model="showFiltersModal" title="Filters" right separator with-close-button>
        <div class="space-y-4">

            <p class="text-center font-semibold">Filter by various criteria:</p>

            {{-- Status Filter --}}
            <x-select 
                wire:model="deliveryStatusFilter" 
                :options="$deliveryStatusOptions" 
                icon="o-flag" 
                placeholder="Select Status" 
                label="Delivery Status"
            />

            {{-- Delivery Person Filter --}}
            <x-select 
                wire:model="deliveryPersonFilter"
                :options="$deliveryPeople"
                placeholder="Select Delivery Person"
                label="Assigned Delivery Person"
            />

            {{-- Date Range Filter --}}
            @php
                $dateConfig = [
                    'altFormat' => 'F j, Y',
                    'dateFormat' => 'Y-m-d',
                ];
            @endphp

            <div>
                <label class="block font-medium mb-1">Created From:</label>
                <x-datepicker 
                    wire:model="startDate" 
                    :config="$dateConfig"
                    placeholder="Start Date" 
                    icon="o-calendar"
                />
            </div>

            <div>
                <label class="block font-medium mb-1">Created To:</label>
                <x-datepicker 
                    wire:model="endDate" 
                    :config="$dateConfig"
                    placeholder="End Date" 
                    icon="o-calendar"
                />
            </div>

        </div>
        <x-slot:actions>
            <x-button label="Close" wire:click="$set('showFiltersModal', false)" />
        </x-slot:actions>
    </x-modal>

    <div class="mb-4 sm:space-x-2 space-y-4 lg:flex lg:space-y-0 lg:items-center lg:justify-between">
        {{-- Filters Button --}}
        <div class="lg:flex-shrink-0">
            <x-button label="Filters" wire:click="openFiltersModal" icon="o-flag" class="w-full lg:w-auto" />
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

    <x-table :headers="$headers" :rows="$orders">
        @scope('cell_id', $order)
            <div>{{ $order->id }}</div>
        @endscope

        @scope('cell_customer', $order)
            <div>{{ $order->customer->full_name ?? 'No Customer' }}</div>
        @endscope

        @scope('cell_delivery_address', $order)
            <div>{{ $order->address_text ?? 'No Address' }}</div>
        @endscope

        @scope('cell_total_price', $order)
            <div>€{{ number_format($order->total_price, 2) }}</div>
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
            <div>{{ $order->currentDelivery()?->deliveryPerson->full_name ?? 'Unassigned' }}</div>
        @endscope

        @scope('cell_actions', $order)
            <div class="flex gap-2">
                @if (in_array($order->status, ['delivered', 'cancelled']))
                    <x-badge value="-" class="badge-error" />
                @elseif ($order->status === 'preparing')
                    {{-- "Mark as Ready" button --}}
                    <x-button wire:click="markAsReady({{ $order->id }})" class="btn-secondary" label="Ready!" />
                @elseif ($order->status === 'ready' && $order->currentDelivery()?->deliveryPerson)
                    <x-button wire:click="reassignDelivery({{ $order->id }})" class="btn-warning" label="Reassign" />
                @elseif ($order->status === 'ready' && !$order->currentDelivery()?->deliveryPerson)
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
            <x-button label="Cancel" wire:click="cancelReassign()" />
            <x-button label="Reassign" class="btn-warning" wire:click="confirmReassign" />
        </x-slot:actions>
    </x-modal>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</x-card>
