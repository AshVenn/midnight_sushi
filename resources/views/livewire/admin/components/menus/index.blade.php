<x-card title="Gestion des Menus" separator>
    <x-slot:menu>
        <x-button 
            label="Ajouter un Menu" 
            link="{{ route('admin.menus.create') }}" 
            class="btn-primary" 
            icon="o-plus" 
            responsive 
        />
    </x-slot:menu>

    {{-- Search Bar --}}
    <div class="mb-4 flex flex-col md:flex-row items-center md:space-x-4">
        {{-- Search input with same approach as Products --}}
        <div class="flex-grow w-full">
            <x-input 
                wire:model.lazy="search" 
                placeholder="Rechercher des menus (nom, slug)..." 
                icon="o-magnifying-glass" 
                autocomplete="off" 
                wire:enter="search"
                autocomplete="off"
            />
        </div>
    </div>

    {{-- Table --}}
    <x-table :headers="$headers" :rows="$menus" :sort-by="$sortBy" with-pagination>
        {{-- Image --}}
        @scope('cell_image', $menu)
            @if($menu->image)
            <x-avatar image="{{$menu->image}}" title="{{$menu->name}}" class="!w-14 !rounded-lg"/>
            @else
            <x-avatar image="{{$menu->image}}" title="{{$menu->name}}" class="!w-14 !rounded-lg"/>
            @endif
        @endscope

        {{-- Name --}}
        @scope('cell_name', $menu)
            <div>{{ $menu->name }}</div>
        @endscope

        {{-- Slug --}}
        @scope('cell_slug', $menu)
            <div>{{ $menu->slug }}</div>
        @endscope

        {{-- Price --}}
        @scope('cell_price', $menu)
            <div>{{ number_format($menu->price, 2) }} CHF</div>
        @endscope

        {{-- Discount Price --}}
        @scope('cell_discount_price', $menu)
            @if($menu->discount_price)
                <div>
                    Promo : {{ number_format($menu->discount_price, 2) }} CHF
                    @if($menu->isOnPromotion())
                        <span class="text-green-600 text-sm">(En cours)</span>
                    @else
                        <span class="text-orange-500 text-sm">(Hors période)</span>
                    @endif
                </div>
            @else
                <div class="text-gray-400">—</div>
            @endif
        @endscope

        {{-- Actions --}}
        @scope('cell_actions', $menu)
            <div class="flex space-x-2">
                {{-- Stop Promotion if discount_price is set --}}
                @if($menu->discount_price)
                    <x-button 
                        label="Stop Promo" 
                        wire:click="stopPromotion({{ $menu->id }})"
                        class="btn-secondary"
                    />
                @endif

                <x-button 
                    wire:click="confirmDelete({{ $menu->id }})" 
                    class="btn-error" 
                    icon="o-trash" 
                />
                <x-button 
                    link="{{ route('admin.menus.edit', $menu) }}" 
                    class="btn-primary" 
                    icon="o-pencil" 
                />
                <x-button 
                    link="{{ route('admin.menus.show', $menu) }}" 
                    class="btn-secondary" 
                    icon="o-eye" 
                />
            </div>
        @endscope
    </x-table>

    {{-- Delete Confirmation Modal --}}
    <x-modal wire:model="showDeleteModal" title="Confirmer la Suppression">
        <p>Êtes-vous sûr(e) de vouloir supprimer <strong>{{ $deletingMenuName }}</strong> ? Cette action est irréversible.</p>
        <x-slot:actions>
            <x-button label="Annuler" wire:click="cancelDelete" />
            <x-button label="Supprimer" class="btn-error" wire:click="deleteMenu" />
        </x-slot:actions>
    </x-modal>
</x-card>
