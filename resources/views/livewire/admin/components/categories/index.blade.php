<x-card title="Category Management" separator>
    <x-slot:menu>
        <x-button label="Add Category" link="{{ route('admin.categories.create') }}" class="btn-primary" icon="o-plus" responsive/>
    </x-slot:menu>

    <div class="flex flex-col md:flex-row md:space-x-4 space-y-4 md:space-y-0 mb-4 items-center">
        {{-- Search Input --}}
        <div class="flex-grow">
            <x-input wire:model.lazy="search" placeholder="Search categories..." icon="o-magnifying-glass" autocomplete="off" wire:enter="search" />
        </div>
    </div>

    {{-- Table Component --}}
    <x-table :headers="$headers" :rows="$categories" :sort-by="$sortBy" with-pagination>
        {{-- Render Name --}}
        @scope('cell_name', $category)
            <div>{{ $category->name }}</div>
        @endscope

        {{-- Render Description --}}
        @scope('cell_description', $category)
            <div>{{ $category->description }}</div>
        @endscope

        {{-- Render Actions --}}
        @scope('cell_actions', $category)
            <div class="flex gap-2">
                <x-button wire:click="confirmDelete({{ $category->id }})" class="btn-error" icon="o-trash" />
                <x-button link="{{ route('admin.categories.edit', $category) }}" class="btn-primary" icon="o-pencil" />
            </div>
        @endscope
    </x-table>

    {{-- Delete Confirmation Modal --}}
    <x-modal wire:model="showDeleteModal" title="Confirm Deletion">
        <p>Are you sure you want to delete the category "{{ $deletingCategoryName }}"?</p>
        <x-slot:actions>
            <x-button label="Cancel" wire:click="cancelDelete" />
            <x-button label="Delete" class="btn-error" wire:click="deleteCategory" />
        </x-slot:actions>
    </x-modal>
</x-card>
