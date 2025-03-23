<!-- resources/views/livewire/admin/settings.blade.php -->
<div class="space-y-4">
    <x-input
        label="Frais de livraison (CHF)"
        wire:model="deliveryFee"
        type="number"
        step="0.50"
        min="0"
        suffix="CHF"
    />
    
    <x-button 
        label="Enregistrer les modifications" 
        icon="o-check" 
        class="btn-primary" 
        wire:click="save" 
        spinner
    />
</div>