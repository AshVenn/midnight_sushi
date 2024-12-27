<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Passer à la caisse</h2>

    <form wire:submit.prevent="placeOrder" class="max-w-lg mx-auto">
        <div class="mb-4">
            <label for="phone_number" class="block y">Numéro de téléphone</label>
            <input type="text" id="phone_number" wire:model="phone_number" class="w-full px-4 py-2 border border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required>
            @error('phone_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="address_text" class="block y">Adresse</label>
            <textarea id="address_text" wire:model="address_text" class="w-full px-4 py-2 border border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" required></textarea>
            @error('address_text') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label for="notes" class="block y">Notes (facultatif)</label>
            <textarea id="notes" wire:model="notes" class="w-full px-4 py-2 border border-primary rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
            @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex justify-end">
            <x-button label="Confirmer la commande" type="submit" class="btn-primary" />
        </div>
    </form>
</div>
