<div class="p-4 md:p-6">
    <x-header title="Vos adresses" separator>
        <x-slot:actions>
            <x-button 
                label="Nouvelle adresse" 
                icon="o-plus" 
                class="btn-primary" 
                wire:click="create"
                :disabled="$addressCount >= 3"
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
                <x-select wire:model="country" label="Pays" :options="[['id' => 'Suisse',
                    'name'=>'Suisse'
                ]]" disabled />

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
                    wire:click="confirmDelete({{ $address->id }})"
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

    <!-- Delete Confirmation Modal -->
    <x-modal wire:model.defer="confirmingDelete" class="backdrop-blur">
        <x-card title="Confirmer la suppression" separator>
            <p>Êtes-vous sûr de vouloir supprimer cette adresse ?</p>
            
            <x-slot:actions>
                <x-button label="Annuler" @click="$wire.confirmingDelete = null" class="btn-ghost" />
                <x-button label="Confirmer" wire:click="delete" class="btn-error" />
            </x-slot:actions>
        </x-card>
    </x-modal>
</div>