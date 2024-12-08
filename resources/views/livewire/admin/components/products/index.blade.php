<x-card title="Product Management">
    <x-slot:menu>
        <x-button label="Add Product" link="{{ route('admin.products.create') }}" class="btn-primary" />
    </x-slot:menu>

    {{-- Search Input --}}
    <x-input wire:model="search" placeholder="Search products..." icon="o-magnifying-glass" />

    {{-- Table --}}
    <x-table :headers="$headers" :rows="$products" with-pagination>
        @scope('cell_image', $product)
            <x-avatar image="{{ $product->image ?? '/empty-image.jpg' }}" class="!w-16 !h-16" />
        @endscope

        @scope('cell_name', $product)
            {{ $product->name }}
        @endscope

        @scope('cell_category.name', $product)
            {{ $product->category->name ?? 'No Category' }}
        @endscope

        @scope('cell_price', $product)
            {{ number_format($product->price, 2) }}
        @endscope

        @scope('cell_actions', $product)
            <x-button wire:click="confirmDelete({{ $product->id }})" class="btn-error" icon="o-trash" />
            <x-button link="{{ route('admin.products.edit', $product) }}" class="btn-primary" icon="o-pencil" />
        @endscope
    </x-table>

    {{-- Delete Confirmation Modal --}}
    <x-modal wire:model="showDeleteModal" title="Confirm Deletion" separator>
        <div class="text-center">
            Are you sure you want to delete <strong>{{ $deletingProductName }}</strong>? This action cannot be undone.
        </div>

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.cancelDelete()" />
            <x-button label="Delete" class="btn-error" wire:click="deleteProduct" />
        </x-slot:actions>
    </x-modal>
</x-card>
