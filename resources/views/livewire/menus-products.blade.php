<div class="p-4 space-y-8" id="menuSection">
    <!-- Search & Filter Section -->
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <x-input 
            icon="o-magnifying-glass" 
            wire:model.live="searchTerm" 
            placeholder="Rechercher menus et produits..." 
            class="flex-1"
        />

        <x-select 
            wire:model.live="selectedAllergen" 
            :options="[
                ['id' => '', 'name' => 'Tous les allergènes'],
                ['id' => 'gluten', 'name' => 'Sans gluten'],
                ['id' => 'lactose', 'name' => 'Sans lactose'],
                ['id' => 'peanuts', 'name' => 'Sans arachides']
            ]" 
            option-label="name" 
            option-value="id"
            class="w-full md:w-64"
        />
    </div>

    <!-- Menus Section -->
    <section class="space-y-4">
        <h2 class="text-2xl font-bold text-primary">Nos Menus</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($menus as $menu)
                <x-card class="hover:shadow-lg transition-shadow duration-200">
                    <img 
                        src="{{ $menu->image }}" 
                        alt="{{ $menu->name }}"
                        class="w-full h-48 object-cover rounded-t-lg"
                        onerror="this.src='{{ asset('images/placeholder.jpg') }}'"
                    />
                    
                    <div class="p-4 space-y-3">
                        <h3 class="text-xl font-semibold">{{ $menu->name }}</h3>
                        
                        <div class="flex items-center gap-2">
                            <x-rating :value="$menu->reviews->avg('rating') ?? 0" class="text-warning" />
                            <span class="text-sm text-gray-600">
                                ({{ $menu->reviews->count() }} avis)
                            </span>
                        </div>

                        <p class="text-gray-600 line-clamp-2">{{ $menu->description }}</p>

                        <div class="flex justify-between items-center">
                            <x-badge :value="number_format($menu->price, 2).' CHF'" class="badge-primary" />
                            <div class="flex gap-2">
                                <x-button 
                                    icon="o-eye" 
                                    class="btn-ghost" 
                                    wire:click="showMenuDetails({{ $menu->id }})"
                                />
                                <x-button 
                                    icon="o-plus" 
                                    class="btn-primary" 
                                    wire:click="addToCart('menu', {{ $menu->id }})"
                                />
                            </div>
                        </div>
                    </div>
                </x-card>
            @empty
                <x-alert icon="o-face-frown" class="col-span-full">
                    Aucun menu trouvé
                </x-alert>
            @endforelse
        </div>
        {{ $menus->links() }}
    </section>

    <!-- Products Section -->
    <section class="space-y-4">
        <h2 class="text-2xl font-bold text-secondary">Nos Produits</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @forelse($products as $product)
                <x-card class="hover:shadow-lg transition-shadow duration-200">
                    <img 
                        src="{{ $product->image }}" 
                        alt="{{ $product->name }}"
                        class="w-full h-48 object-cover rounded-t-lg"
                        onerror="this.src='{{ asset('images/placeholder.jpg') }}'"
                    />
                    
                    <div class="p-4 space-y-3">
                        <h3 class="text-xl font-semibold">{{ $product->name }}</h3>
                        
                        <div class="flex items-center gap-2">
                            <x-rating :value="$product->reviews->avg('rating') ?? 0" class="text-accent" />
                            <span class="text-sm text-gray-600">
                                ({{ $product->reviews->count() }} avis)
                            </span>
                        </div>

                        <p class="text-sm text-gray-600">{{ $product->category->name }}</p>

                        <div class="flex justify-between items-center">
                            <x-badge 
                                :value="number_format($product->final_price, 2).' CHF'" 
                                :class="'badge-'.($product->isOnPromotion() ? 'warning' : 'secondary')" 
                            />
                            <x-button 
                                icon="o-plus" 
                                class="btn-secondary" 
                                wire:click="addToCart('product', {{ $product->id }})"
                            />
                        </div>
                    </div>
                </x-card>
            @empty
                <x-alert icon="o-face-frown" class="col-span-full">
                    Aucun produit trouvé
                </x-alert>
            @endforelse
        </div>
        {{ $products->links() }}
    </section>

    <!-- Menu Details Modal -->
    @livewire('menu-details-modal')
</div>