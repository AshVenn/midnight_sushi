<div class="p-4 md:p-6">
    <x-header title="Vos adresses" separator>
        <x-slot:actions>
            <x-button label="Nouvelle adresse" link="/customer/addresses/create" icon="o-plus" class="btn-primary" />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($addresses as $address)
            <x-card class="bg-base-200">
                <x-list-item :item="$address" sub-value="full_address" no-separator>
                    <x-slot:avatar>
                        <x-icon name="o-map-pin" class="text-primary text-xl" />
                    </x-slot:avatar>
                    <x-slot:actions>
                        <x-button icon="o-trash" class="btn-ghost btn-xs text-error" />
                    </x-slot:actions>
                </x-list-item>
            </x-card>
        @endforeach
    </div>

    {{ $addresses->links() }}
</div>