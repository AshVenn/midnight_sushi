<x-card title="Deliveries Management" separator>
    {{-- Filters Modal --}}
    <x-modal wire:model="showFiltersModal" title="Filters" right separator with-close-button>
        <div class="space-y-4">
            <p class="text-center font-semibold">Filter by various criteria:</p>
            
            {{-- Status Filter --}}
            <x-select 
                wire:model="statusFilter" 
                :options="$deliveryStatusOptions" 
                placeholder="Select Status" 
                label="Status"
            />

            {{-- Delivery Person Filter --}}
            <x-select 
                wire:model="deliveryPersonFilter" 
                :options="$deliveryPeople" 
                placeholder="Select Delivery Person" 
                label="Delivery Person"
            />

            {{-- Date Range Filter --}}
            @php
                // Configuration for a regular date picker (no special modes)
                $dateConfig = [
                    'altFormat' => 'F j, Y',
                    'dateFormat' => 'Y-m-d',
                ];
            @endphp

            <div>
                <label class="block font-medium mb-1">Assigned From:</label>
                <x-datepicker 
                    wire:model="startDate" 
                    :config="$dateConfig"
                    placeholder="Select Start Date" 
                    icon="o-calendar"
                />
            </div>

            <div>
                <label class="block font-medium mb-1">Assigned To:</label>
                <x-datepicker 
                    wire:model="endDate" 
                    :config="$dateConfig"
                    placeholder="Select End Date" 
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
                placeholder="Search by order ID or customer..." 
                icon="o-magnifying-glass" 
                class="w-full"
            />
        </div>
    </div>


    {{-- Table Component --}}
    <x-table :headers="$headers" :rows="$deliveries">
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
            <div>{{ $delivery->deliveryPerson->full_name ?? 'Unassigned' }}</div>
        @endscope

        @scope('cell_status', $delivery)
            @php
                $statusClass = match($delivery->status) {
                    'assigned' => 'badge-warning',
                    'picked_up' => 'badge-primary',
                    'delivered' => 'badge-success',
                    default => 'badge-secondary',
                };
            @endphp
            <x-badge :value="ucfirst(str_replace('_', ' ', $delivery->status))" :class="$statusClass" />
        @endscope
        
        @scope('cell_assigned_at', $delivery)
            <div>{{ $delivery->assigned_at ? $delivery->assigned_at->format('Y-m-d H:i') : 'N/A' }}</div>
        @endscope

        @scope('cell_delivered_at', $delivery)
            <div>{{ $delivery->delivered_at ? $delivery->delivered_at->format('Y-m-d H:i') : '-' }}</div>
        @endscope


        @scope('cell_actions', $delivery)
            <div class="flex gap-2">
                {{-- Reassign Button --}}
                <x-button wire:click="reassignDelivery({{ $delivery->id }})" class="btn-warning" label="Reassign" />

                {{-- Additional Actions (Picked Up, Delivered) --}}
                @if($delivery->status === 'assigned' || $delivery->status === 'picked_up')
                    <x-button wire:click="performAction({{ $delivery->id }})" class="btn-primary" label="Actions" />
                @else
                    <x-badge value="-" class="badge-secondary" />
                @endif
            </div>
        @endscope
    </x-table>

    {{-- Reassign Modal --}}
    <x-modal wire:model="showReassignModal" title="Reassign Delivery" separator>
        <div class="text-center">
            <x-select wire:model="selectedDeliveryPerson" :options="$deliveryPeople" placeholder="Select Delivery Person" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.cancelReassign()" />
            <x-button label="Reassign" class="btn-warning" wire:click="confirmReassign" />
        </x-slot:actions>
    </x-modal>

    {{-- Actions Modal --}}
    <x-modal wire:model="showActionModal" title="Perform Action" separator>
        <div class="text-center">
            <x-select wire:model="selectedAction" :options="$actionsOptions" placeholder="Select Action" />
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.cancelAction()" />
            <x-button label="Confirm" class="btn-primary" wire:click="confirmAction" />
        </x-slot:actions>
    </x-modal>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $deliveries->links() }}
    </div>
</x-card>
