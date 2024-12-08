<x-card title="Edit Category">
    <x-form wire:submit.prevent="save">
        {{-- Name --}}
        <x-input 
            wire:model="name" 
            label="Category Name" 
            placeholder="Enter the category name" 
        />

        {{-- Description --}}
        <x-textarea 
            wire:model="description" 
            label="Description" 
            placeholder="Enter a brief description of the category" 
            rows="5" 
        />

        {{-- Action Buttons --}}
        <x-slot:actions>
            <x-button label="Cancel" link="{{ route('admin.categories.index') }}" />
            <x-button label="Save Changes" type="submit" class="btn-primary" />
        </x-slot:actions>
    </x-form>
</x-card>
