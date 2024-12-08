<x-card title="Category Management" separator>
    <x-slot:menu>
        <x-button label="Create Category" link="{{ route('admin.categories.create') }}" class="btn-primary" />
    </x-slot:menu>

    <x-input wire:model="search" placeholder="Search categories..." icon="o-magnifying-glass" />

        <x-table :headers="$headers" :rows="$categories" with-pagination>
            @scope('name', $category)
                <div>{{ $category->name }}</div>
            @endscope

            @scope('description', $category)
                <div>{{ $category->description }}</div>
            @endscope

            @scope('actions', $category)
                <div class="flex gap-2">
                    <x-button wire:click="confirmDelete({{ $category->id }})" class="btn-error" icon="o-trash" />
                    <x-button link="{{ route('admin.categories.edit', $category) }}" class="btn-primary" icon="o-pencil" />
                </div>
            @endscope
        </x-table>

        <x-modal wire:model="showDeleteModal">
            <div>
                <p>Are you sure you want to delete the category "{{ $deletingCategoryName }}"?</p>
            </div>
            <x-slot:actions>
                <x-button label="Cancel" @click="cancelDelete" />
                <x-button label="Delete" class="btn-error" wire:click="deleteCategory" />
            </x-slot:actions>
        </x-modal>
</x-card>
