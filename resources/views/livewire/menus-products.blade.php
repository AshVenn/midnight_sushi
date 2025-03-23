<div class="p-4 space-y-8" id="menuSection">
    <!-- Search & Filter Section -->
    <div class="mb-4 sm:space-x-2 space-y-4 lg:flex lg:space-y-0 lg:items-center lg:justify-between">
        <div class="lg:flex-shrink-0">
            <x-input 
                icon="o-magnifying-glass" 
                wire:model.live="searchTerm" 
                placeholder="Rechercher menus et produits..." 
                class="flex-1 w-full"
                autocomplete="off"
            />
        </div>

        <div class="lg:flex-grow">
            <x-select
                wire:model.live="selectedAllergen" 
                :options="$allAllergens" 
                option-label="name" 
                option-value="id"
                class="w-full md:w-64"
                placeholder="Filtrer par allergène"
            />
        </div>
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
                        <h3 class="text-xl font-semibold">
                            {{ $menu->name }}
                        </h3>
                        
                        <!-- Dynamic star rating for Menus -->
                        <div class="flex items-center gap-2">
                            <div class="flex items-center">
                                @php
                                    $rating = $menu->reviews_avg_rating ?? 0;
                                @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if($rating >= $i)
                                        <!-- Full star -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                        </svg>
                                    @elseif($rating >= $i - 0.5)
                                        <!-- Half star using overlay -->
                                        <div class="relative inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                            </svg>
                                            <div class="absolute top-0 left-0 overflow-hidden" style="width:50%;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Empty star -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600">
                                ({{ $menu->reviews->count() }} avis)
                            </span>
                        </div>
                        <!-- End rating block -->

                        <p class="text-gray-600 line-clamp-2">
                            {{ $menu->description }}
                        </p>

                        <div class="flex justify-between items-center">
                            <x-badge 
                                :value="number_format($menu->price, 2).' CHF'" 
                                class="badge-primary" 
                            />
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
                        <h3 class="text-xl font-semibold">
                            {{ $product->name }}
                        </h3>
                        
                        <!-- Dynamic star rating for Products -->
                        <div class="flex items-center gap-2">
                            <div class="flex items-center">
                                @php
                                    $rating = $product->reviews_avg_rating ?? 0;
                                @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    @if($rating >= $i)
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                        </svg>
                                    @elseif($rating >= $i - 0.5)
                                        <div class="relative inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                            </svg>
                                            <div class="absolute top-0 left-0 overflow-hidden" style="width:50%;">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                                </svg>
                                            </div>
                                        </div>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600">
                                ({{ $product->reviews->count() }} avis)
                            </span>
                        </div>
                        <!-- End rating block -->

                        <p class="text-sm text-gray-600">
                            {{ $product->category->name }}
                        </p>

                        <div class="flex justify-between items-center">
                            <x-badge 
                                :value="number_format($product->final_price, 2).' CHF'" 
                                :class="'badge-'.($product->isOnPromotion() ? 'warning' : 'secondary')" 
                            />
                            <div class="flex gap-2">
                                <x-button 
                                    icon="o-eye" 
                                    class="btn-ghost" 
                                    wire:click="showProductDetails({{ $product->id }})"
                                />
                                <x-button 
                                    icon="o-plus" 
                                    class="btn-secondary" 
                                    wire:click="addToCart('product', {{ $product->id }})"
                                />
                            </div>
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

    <!-- Menu Details Drawer -->
    <x-drawer 
        wire:model="showMenuDrawer" 
        class="w-11/12 lg:w-1/3" 
        title="Détails du menu" 
        separator
        with-close-button
        right
    >
        @if($selectedMenu)
            <div class="space-y-6">
                <x-badge :value="number_format($selectedMenu->price, 2).' CHF'" class="badge-primary" />
                <img 
                    src="{{ $selectedMenu->image }}" 
                    alt="{{ $selectedMenu->name }}"
                    class="w-full h-48 object-cover rounded-lg"
                    onerror="this.src='{{ asset('images/placeholder.jpg') }}'"
                />

                <div>
                    <h3 class="font-bold">Description</h3>
                    <p>{{ $selectedMenu->description }}</p>
                </div>

                <div>
                    <h3 class="font-bold">Contenu du menu</h3>
                    <ul class="space-y-2">
                        @foreach($selectedMenu->products as $product)
                            <li class="flex justify-between items-center p-2 bg-base-200 rounded">
                                <span>{{ $product->name }}</span>
                                <span class="text-sm text-gray-600">
                                    {{ number_format($product->price, 2) }} CHF
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Menu drawer rating -->
                <div>
                    <h3 class="font-bold">Avis</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex items-center">
                            @php
                                $rating = $selectedMenu->reviews_avg_rating ?? 0;
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @if($rating >= $i)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                    </svg>
                                @elseif($rating >= $i - 0.5)
                                    <div class="relative inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                        </svg>
                                        <div class="absolute top-0 left-0 overflow-hidden" style="width:50%;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                            </svg>
                                        </div>
                                    </div>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">
                            ({{ $selectedMenu->reviews->count() }} avis)
                        </span>
                    </div>
                    <!-- Removed review comment display -->
                </div>

                <x-slot:actions>
                    <x-button label="Fermer" @click="$wire.showMenuDrawer = false" class="btn-ghost" />
                    <x-button 
                        label="Ajouter au panier" 
                        icon="o-shopping-cart" 
                        wire:click="addToCart('Menu', {{ $selectedMenu->id }})" 
                        class="btn-primary" 
                    />
                </x-slot:actions>
            @endif
        </x-drawer>

    <!-- Product Details Drawer -->
    <x-drawer 
        wire:model="showProductDrawer" 
        class="w-11/12 lg:w-1/3" 
        title="Détails du produit" 
        separator
        right
        with-close-button
    >
        @if($selectedProduct)
            <div class="space-y-6">
                <img 
                    src="{{ $selectedProduct->image }}" 
                    alt="{{ $selectedProduct->name }}"
                    class="w-full h-48 object-cover rounded-lg"
                    onerror="this.src='{{ asset('images/placeholder.jpg') }}'"
                />

                <div>
                    <h3 class="font-bold">Prix</h3>
                    <x-badge 
                        :value="number_format($selectedProduct->final_price, 2).' CHF'" 
                        :class="'badge-'.($selectedProduct->isOnPromotion() ? 'warning' : 'secondary')" 
                    />
                </div>

                <div>
                    <h3 class="font-bold">Allergènes</h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse($selectedProduct->allergens ?? [] as $allergen)
                            <x-badge :value="$allergen" class="badge-outline badge-sm" />
                        @empty
                            <span class="text-sm text-gray-500">
                                Aucun allergène déclaré
                            </span>
                        @endforelse
                    </div>
                </div>

                <!-- Product drawer rating -->
                <div>
                    <h3 class="font-bold">Avis</h3>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex items-center">
                            @php
                                $rating = $selectedProduct->reviews_avg_rating ?? 0;
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @if($rating >= $i)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                    </svg>
                                @elseif($rating >= $i - 0.5)
                                    <div class="relative inline-block">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                        </svg>
                                        <div class="absolute top-0 left-0 overflow-hidden" style="width:50%;">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                            </svg>
                                        </div>
                                    </div>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.973a1 1 0 00.95.69h4.177c.969 0 1.371 1.24.588 1.81l-3.38 2.455a1 1 0 00-.364 1.118l1.286 3.973c.3.921-.755 1.688-1.54 1.118l-3.38-2.455a1 1 0 00-1.175 0l-3.38 2.455c-.784.57-1.838-.197-1.54-1.118l1.286-3.973a1 1 0 00-.364-1.118L2.05 9.4c-.783-.57-.38-1.81.588-1.81h4.177a1 1 0 00.95-.69l1.286-3.973z" />
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">
                            ({{ $selectedProduct->reviews->count() }} avis)
                        </span>
                    </div>
                    
                </div>

                <x-slot:actions>
                    <x-button label="Fermer" @click="$wire.showProductDrawer = false" class="btn-ghost" />
                    <x-button 
                        label="Ajouter au panier" 
                        icon="o-shopping-cart" 
                        wire:click="addToCart('Product', {{ $selectedProduct->id }})" 
                        class="btn-secondary" 
                    />
                </x-slot:actions>
            @endif
        </x-drawer>
</div>
