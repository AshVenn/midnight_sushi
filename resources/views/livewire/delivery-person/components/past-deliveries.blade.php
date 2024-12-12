<div>
    <x-card title="Mes Livraisons Passées" separator>
        {{-- Filtre et Recherche --}}
        <div class="mb-4 flex flex-wrap gap-4 lg:justify-between">
            {{-- Champ de Recherche --}}
            <x-input
                wire:model.lazy="search"
                placeholder="Rechercher par ID ou client"
                icon="o-magnifying-glass"
                class="flex-grow w-full"
                autocomplete="off"
            />

            {{-- Filtres de Date --}}
            @php
                $dateConfig = [
                    'altFormat' => 'j F, Y',
                    'dateFormat' => 'Y-m-d',
                ];
            @endphp

            <x-datepicker
                wire:model="startDate"
                :config="$dateConfig"
                placeholder="Date Début"
                icon="o-calendar"
                class="flex-grow lg:flex-none w-full"
            />

            <x-datepicker
                wire:model="endDate"
                :config="$dateConfig"
                placeholder="Date Fin"
                icon="o-calendar"
                class="flex-grow lg:flex-none w-full"
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
                <div>{{ $delivery->address_text ?? 'Adresse non disponible' }}</div>
            @endscope

            @scope('cell_delivered_at', $delivery)
                <div>{{ $delivery->delivered_at ? $delivery->delivered_at->format('d/m/Y H:i') : '-' }}</div>
            @endscope
        </x-table>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $deliveries->links() }}
        </div>
    </x-card>
</div>
