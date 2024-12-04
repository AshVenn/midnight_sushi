
    <div>
        <h2 class="text-2xl font-bold text-center mb-4">Welcome Back!</h2>
        <x-form wire:submit="login">
            <x-input
                label="Email"
                type="email"
                wire:model="email"
                required
            />

            <x-input
                label="Password"
                type="password"
                wire:model="password"
                required
            />

            <x-slot:actions>
                <div class="flex flex-col items-start space-y-2">
                    <x-button
                        label="Login"
                        class="btn-primary"
                        type="submit"
                        spinner="login"
                    />
                    <x-button
                        label="Don’t have an account? Register"
                        link="/register"
                        class="btn-ghost"
                    />
                </div>
            </x-slot:actions>
        </x-form>
    </div>

