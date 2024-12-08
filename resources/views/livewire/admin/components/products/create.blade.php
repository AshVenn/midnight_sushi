<x-card title="Create Product">
    <x-form wire:submit.prevent="save">
        {{-- Product Name --}}
        <x-input wire:model="name" label="Name" placeholder="Enter product name" />

        {{-- Price --}}
        <x-input wire:model="price" label="Price" type="number" step="0.01" placeholder="Enter product price" />

        {{-- Description --}}
        <x-textarea wire:model="description" label="Description" placeholder="Enter product description" rows="4" />

        {{-- Allergens --}}
        <x-input wire:model="allergens" label="Allergens" placeholder="Enter allergens (optional)" />

        {{-- Category --}}
        <x-select wire:model="category_id" label="Category" :options="$categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])" placeholder="Select a category" />

        {{-- Image Upload --}}
        <x-file wire:model="image" label="Image" accept="image/*"/>
        

        @if ($image)
                <img src="{{ $image->temporaryUrl() }}" class="h-40 rounded-lg mt-2" alt="Preview" />
        @endif

        {{-- Action Buttons --}}
        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('admin.products.index') }}" />
            <x-button label="Create Product" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
