<div class="p-4 sm:p-4 md:p-6">
    <x-card title="Profil Client" separator>
        <x-form wire:submit.prevent="save" class="space-y-4 sm:space-y-6">
            <!-- Row 1: First Name & Last Name -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input
                    wire:model="first_name"
                    label="Prénom"
                    placeholder="Entrez votre prénom"
                    class="w-full"
                />
                <x-input
                    wire:model="last_name"
                    label="Nom"
                    placeholder="Entrez votre nom"
                    class="w-full"
                />
            </div>

            <!-- Row 2: Email & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input
                    wire:model="email"
                    label="Email"
                    type="email"
                    placeholder="Entrez votre email"
                    class="w-full"
                />
                <x-input
                    wire:model="phone_number"
                    label="Numéro de Téléphone"
                    placeholder="Entrez votre numéro"
                    class="w-full"
                />
            </div>

            <!-- Row 3: Password & Confirm Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-input
                    wire:model="password"
                    label="Mot de Passe"
                    type="password"
                    placeholder="Nouveau mot de passe (facultatif)"
                    class="w-full"
                />
                <x-input
                    wire:model="confirm_password"
                    label="Confirmation"
                    type="password"
                    placeholder="Confirmez le mot de passe"
                    class="w-full"
                />
            </div>

            <!-- Avatar Upload -->
            <div class="space-y-4">
                <x-file
                    wire:model="avatar"
                    label="Photo de Profil"
                    accept="image/*"
                    class="w-full"
                />

                <div class="flex flex-col sm:flex-row gap-4">
                    @if ($oldAvatarUrl)
                        <img
                            src="{{ $oldAvatarUrl }}"
                            class="h-40 w-full sm:w-1/3 object-cover rounded-lg"
                            alt="Avatar actuel"
                        />
                    @endif

                    @if ($avatar)
                        <img
                            src="{{ $avatar->temporaryUrl() }}"
                            class="h-40 w-full sm:w-1/3 object-cover rounded-lg"
                            alt="Nouvel avatar"
                        />
                    @endif
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex flex-col sm:flex-row items-center sm:justify-end mt-6">
                <x-button
                    label="Sauvegarder"
                    type="submit"
                    class="btn-primary w-full sm:w-auto"
                    icon="o-check"
                    spinner
                />
            </div>
        </x-form>

        <x-toast />
    </x-card>
</div>