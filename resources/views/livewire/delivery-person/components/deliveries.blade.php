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
                <x-badge :value="match ($delivery->status) {
                    'assigned' => 'Attribuée',
                    'picked_up' => 'Ramassée',
                    default => ucfirst($delivery->status),
                }" class="{{ match ($delivery->status) {
                    'assigned' => 'badge-primary',
                    'picked_up' => 'badge-warning',
                    default => 'badge-secondary',
                } }}" />
            @endscope

            @scope('cell_assigned_at', $delivery)
                <div>{{ $delivery->assigned_at ? $delivery->assigned_at->format('d/m/Y H:i') : 'N/A' }}</div>
            @endscope

            @scope('cell_actions', $delivery)
                <div class="flex gap-2">
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
</div>
