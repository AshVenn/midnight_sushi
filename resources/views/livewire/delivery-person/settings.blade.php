<div class="p-4 md:p-6">
    <x-card title="Paramètres du Livreur" separator>
        <x-form wire:submit.prevent="save" class="space-y-6">
            <!-- Row 1: Prénom & Nom -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    wire:model="first_name"
                    label="Prénom"
                    placeholder="Entrez votre prénom"
                />
                <x-input
                    wire:model="last_name"
                    label="Nom"
                    placeholder="Entrez votre nom"
                />
            </div>

            <!-- Row 2: Email & Téléphone -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    wire:model="email"
                    label="Email"
                    type="email"
                    placeholder="Entrez votre email"
                />
                <x-input
                    wire:model="phone_number"
                    label="Numéro de Téléphone"
                    placeholder="Entrez votre numéro de téléphone"
                />
            </div>


            <!-- Avatar Upload + Preview -->
            <div class="space-y-4">
                <x-file
                    wire:model="avatar"
                    label="Photo de Profil"
                    accept="image/*"
                />

                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Aperçu de l'Avatar (Ancien) -->
                    @if ($oldAvatarUrl)
                        <img
                            src="{{ $oldAvatarUrl }}"
                            class="h-40 w-full md:w-1/2 object-cover rounded-lg"
                            alt="Avatar Actuel"
                        />
                    @endif

                    <!-- Aperçu de l'Avatar (Nouveau) -->
                    @if ($avatar)
                        <img
                            src="{{ $avatar->temporaryUrl() }}"
                            class="h-40 w-full md:w-1/2 object-cover rounded-lg"
                            alt="Aperçu de l'Avatar"
                        />
                    @endif
                </div>
            </div>

            <!-- Boutons d'Action -->
            <div class="flex flex-col md:flex-row md:justify-end gap-4 mt-6">
                <x-button
                    label="Sauvegarder les Modifications"
                    type="submit"
                    class="btn-primary w-full md:w-auto"
                />
            </div>
        </x-form>

        <!-- Toast Message -->
        <x-toast />
    </x-card>
</div>
