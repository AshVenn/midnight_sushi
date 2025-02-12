
    <x-card title="Demandes de Contact" separator>
        <div class="flex justify-between items-center mb-4">
            <div class="w-full md:w-1/3">
                <x-select 
                    wire:model="filterStatus" 
                    :options="$filterStatusOptions"
                    placeholder="Filtrer par statut"
                    wire:change="applyFilter"
                />
            </div>

            <!-- Clear All button -->
            <div>
                <x-button
                    label="Effacer tout"
                    icon="o-trash"
                    color="error"
                    wire:click="clearAll()"
                />
            </div>
        </div>

        {{-- Table Component --}}
        <x-table :headers="$headers" :rows="$inquiries" :sort-by="$sortBy" with-pagination>
            {{-- Colonne ID --}}
            @scope('cell_id', $inquiry)
                <div>{{ $inquiry->id }}</div>
            @endscope

            {{-- Colonne Nom --}}
            @scope('cell_name', $inquiry)
                <div>{{ $inquiry->name }}</div>
            @endscope

            {{-- Colonne Email --}}
            @scope('cell_email', $inquiry)
                <div>{{ $inquiry->email }}</div>
            @endscope

            {{-- Colonne Sujet --}}
            @scope('cell_subject', $inquiry)
                <div>{{ $inquiry->subject ?? 'Sans sujet' }}</div>
            @endscope

            {{-- Colonne Message --}}
            @scope('cell_message', $inquiry)
                <div>{{ \Illuminate\Support\Str::limit($inquiry->message, 50) }}</div>
            @endscope

            {{-- Colonne Statut --}}
            @scope('cell_status', $inquiry)
                @php
                    // Map status values to French translations
                    $statusTranslations = [
                        'open' => 'Ouverte',
                        'resolved' => 'Résolue',
                    ];
                    $translatedStatus = $statusTranslations[$inquiry->status] ?? $inquiry->status;
                @endphp
                <x-badge :value="$translatedStatus" class="{{ $inquiry->status === 'open' ? 'badge-error' : 'badge-success' }}" />
            @endscope

            {{-- Colonne Date --}}
            @scope('cell_created_at', $inquiry)
                <div>{{ $inquiry->created_at->format('d/m/Y') }}</div>
            @endscope

            {{-- Colonne Actions --}}
            @scope('cell_actions', $inquiry)
                <div class="flex gap-2">
                    @if($inquiry->status === 'open')
                        <x-button
                            size="xs"
                            icon="o-check"
                            wire:click="markAsResolved({{ $inquiry->id }})"
                        />
                    @else
                        <span class="text-sm text-gray-500">Résolu</span>
                    @endif
                </div>
            @endscope
        </x-table>
    </x-card>
