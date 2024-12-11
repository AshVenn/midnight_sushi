<x-card title="Product Management">
    <x-slot:menu>
        <x-button label="Add Product" link="{{ route('admin.products.create') }}" class="btn-primary" icon="o-plus" responsive />
    </x-slot:menu>

    {{-- Filters --}}
    <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-4 w-full">
        {{-- Search Input --}}
        <div class="w-full">
            <x-input wire:model.lazy="search" placeholder="Search products..." icon="o-magnifying-glass" autocomplete="off" />
        </div>

        {{-- Category Filter --}}
        <div class="w-full md:w-1/3">
            <x-select wire:model="categoryFilter" :options="$categories" placeholder="Filter by Category" wire:change="updateCategoryFilter" />
        </div>
    </div>

    {{-- Table --}}
    <x-table :headers="$headers" :rows="$products" :sort-by="$sortBy" with-pagination>
        {{-- Image Column --}}
        @scope('cell_image', $product)
            <x-avatar image="{{ $product->image ?? '/empty-image.jpg' }}" class="!w-16 !h-16" />
        @endscope

        {{-- Name Column --}}
        @scope('cell_name', $product)
            {{ $product->name }}
        @endscope

        {{-- Category Column --}}
        @scope('cell_category.name', $product)
            {{ $product->category->name ?? 'No Category' }}
        @endscope

        {{-- Price Column --}}
        @scope('cell_price', $product)
            {{ number_format($product->price, 2) }}
        @endscope

        {{-- Actions Column --}}
        @scope('cell_actions', $product)
            <x-button wire:click="confirmDelete({{ $product->id }})" class="btn-error" icon="o-trash" />
            <x-button link="{{ route('admin.products.edit', $product) }}" class="btn-primary" icon="o-pencil" />
        @endscope
    </x-table>

    {{-- Delete Confirmation Modal --}}
    <x-modal wire:model="showDeleteModal" title="Confirm Deletion">
        <p>Are you sure you want to delete <strong>{{ $deletingProductName }}</strong>? This action cannot be undone.</p>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="cancelDelete" />
            <x-button label="Delete" class="btn-error" wire:click="deleteProduct" />
        </x-slot:actions>
    </x-modal>
</x-card>
