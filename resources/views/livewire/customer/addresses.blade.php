<!-- resources/views/livewire/customer/addresses.blade.php -->
<div class="p-4 md:p-6">
    <x-header title="Vos adresses" separator>
        <x-slot:actions>
            <x-button 
                label="Nouvelle adresse" 
                icon="o-plus" 
                class="btn-primary" 
                wire:click="create"
                :disabled="$addresses->total() >= 3"
            />
        </x-slot:actions>
    </x-header>

    @if($showForm)
        <x-card class="mb-6">
            <x-form wire:submit.prevent="save">
                <x-input wire:model="recipient_name" label="Nom du destinataire" />
                <x-input wire:model="street_address" label="Adresse" />
                <div class="grid grid-cols-2 gap-4">
                    <x-input wire:model="city" label="Ville" />
                    <x-input wire:model="postal_code" label="Code postal" />
                </div>
                <x-select wire:model="country" label="Pays" :options="['Suisse' => 'Suisse']" disabled />

                <x-slot:actions>
                    <x-button label="Annuler" @click="cancel()" class="btn-ghost" />
                    <x-button label="Enregistrer" type="submit" class="btn-primary" />
                </x-slot:actions>
            </x-form>
        </x-card>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($addresses as $address)
            <x-card class="relative">
                <x-icon name="o-trash" 
                    class="absolute top-2 right-2 cursor-pointer text-error hover:text-error/70"
                    wire:click="delete({{ $address->id }})"
                    wire:confirm="Êtes-vous sûr de vouloir supprimer cette adresse ?"
                />
                
                <x-list-item :item="$address" sub-value="full_address" no-separator>
                    <x-slot:avatar>
                        <x-icon name="o-map-pin" class="text-primary text-xl" />
                    </x-slot:avatar>
                    <x-slot:actions>
                        <x-button 
                            icon="o-pencil" 
                            class="btn-ghost btn-xs" 
                            wire:click="edit({{ $address->id }})"
                        />
                    </x-slot:actions>
                </x-list-item>
            </x-card>
        @empty
            <x-alert icon="o-map-pin" description="Aucune adresse enregistrée" />
        @endforelse
    </div>

    {{ $addresses->links() }}
</div>