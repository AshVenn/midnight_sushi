<x-card title="Product Management" separator>
    <x-slot:menu>
        <x-button label="Create Product" link="{{ route('admin.products.create') }}" class="btn-primary" />
    </x-slot:menu>

    <x-input wire:model="search" placeholder="Search products..." icon="o-magnifying-glass" />

    <x-table :headers="['Name', 'Price', 'Category', 'Actions']" :rows="$products" with-pagination>
        @scope('name', $product)
            <div>{{ $product->name }}</div>
        @endscope

        @scope('price', $product)
            <div>${{ $product->price }}</div>
        @endscope

        @scope('category', $product)
            <div>{{ $product->category->name }}</div>
        @endscope

        @scope('actions', $product)
            <div class="flex gap-2">
                <x-button wire:click="delete({{ $product->id }})" class="btn-error" icon="o-trash" />
                <x-button link="{{ route('admin.products.edit', ['product' => $product->id]) }}" class="btn-primary" icon="o-pencil" />
            </div>
        @endscope
    </x-table>
</x-card>
