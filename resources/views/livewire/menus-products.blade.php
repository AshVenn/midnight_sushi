<div class="p-4 space-y-8" id="menuSection">
    <!-- Search & Filter Section -->
    <div class="mb-4 sm:space-x-2 space-y-4 lg:flex lg:space-y-0 lg:items-center lg:justify-between">
        <div class="lg:flex-shrink-0">
            <x-input 
                icon="o-magnifying-glass" 
                wire:model.live="searchTerm" 
                placeholder="Rechercher menus et produits..." 
                class="flex-1 w-full"
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
                        
                        <!-- Updated dynamic star rating for Menus -->
                        <div class="flex items-center gap-2">
                            <div class="rating rating-md">
                                @php
                                    $rating = $menu->reviews_avg_rating ?? 0;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                @endphp

                                <!-- Full Stars -->
                                @for ($i = 0; $i < $fullStars; $i++)
                                    <input type="radio" name="menu-rating-{{ $menu->id }}" 
                                           class="mask mask-star-2 bg-primary" checked disabled />
                                @endfor

                                <!-- Half Star -->
                                @if ($hasHalfStar)
                                    <input type="radio" name="menu-rating-{{ $menu->id }}" 
                                           class="mask mask-star-2 bg-primary mask-half-1" 
                                           checked disabled />
                                    <input type="radio" name="menu-rating-{{ $menu->id }}" 
                                           class="mask mask-star-2 bg-primary mask-half-2" 
                                           checked disabled />
                                @endif

                                <!-- Empty Stars -->
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <input type="radio" name="menu-rating-{{ $menu->id }}" 
                                           class="mask mask-star-2 bg-gray-300" disabled />
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600">
                                ({{ $menu->reviews->count() }} avis)
                            </span>
                        </div>
                        <!-- End updated rating block -->

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
                        
                        <!-- Updated dynamic star rating for Products -->
                        <div class="flex items-center gap-2">
                            <div class="rating rating-md">
                                @php
                                    $rating = $product->reviews_avg_rating ?? 0;
                                    $fullStars = floor($rating);
                                    $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                @endphp

                                <!-- Full Stars -->
                                @for ($i = 0; $i < $fullStars; $i++)
                                    <input type="radio" name="product-rating-{{ $product->id }}" 
                                           class="mask mask-star-2 bg-primary" checked disabled />
                                @endfor

                                <!-- Half Star -->
                                @if ($hasHalfStar)
                                    <input type="radio" name="product-rating-{{ $product->id }}" 
                                           class="mask mask-star-2 bg-primary mask-half-1" 
                                           checked disabled />
                                    <input type="radio" name="product-rating-{{ $product->id }}" 
                                           class="mask mask-star-2 bg-primary mask-half-2" 
                                           checked disabled />
                                @endif

                                <!-- Empty Stars -->
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <input type="radio" name="product-rating-{{ $product->id }}" 
                                           class="mask mask-star-2 bg-gray-300" disabled />
                                @endfor
                            </div>
                            <span class="text-sm text-gray-600">
                                ({{ $product->reviews->count() }} avis)
                            </span>
                        </div>
                        <!-- End updated rating block -->

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
                        <div class="rating rating-md">
                            @php
                                $rating = $selectedMenu->reviews_avg_rating ?? 0;
                                $fullStars = floor($rating);
                                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            @endphp

                            <!-- Full Stars -->
                            @for ($i = 0; $i < $fullStars; $i++)
                                <input type="radio" name="drawer-menu-rating-{{ $selectedMenu->id }}" 
                                       class="mask mask-star-2 bg-primary" checked disabled />
                            @endfor

                            <!-- Half Star -->
                            @if ($hasHalfStar)
                                <input type="radio" name="drawer-menu-rating-{{ $selectedMenu->id }}" 
                                       class="mask mask-star-2 bg-primary mask-half-1" 
                                       checked disabled />
                                <input type="radio" name="drawer-menu-rating-{{ $selectedMenu->id }}" 
                                       class="mask mask-star-2 bg-primary mask-half-2" 
                                       checked disabled />
                            @endif

                            <!-- Empty Stars -->
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <input type="radio" name="drawer-menu-rating-{{ $selectedMenu->id }}" 
                                       class="mask mask-star-2 bg-gray-300" disabled />
                            @endfor
                        </div>
                        <span class="text-gray-600">
                            ({{ $selectedMenu->reviews->count() }} avis)
                        </span>
                    </div>

                    <div class="space-y-4">
                        @foreach($selectedMenu->reviews->take(3) as $review)
                            <div class="bg-base-100 p-4 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="font-bold">
                                        {{ $review->user->name }}
                                    </div>
                                    <div class="rating rating-xs">
                                        @php
                                            $drawerRating = $review->rating;
                                            $drawerFull = floor($drawerRating);
                                            $drawerEmpty = 5 - $drawerFull;
                                        @endphp

                                        @for($j = 0; $j < $drawerFull; $j++)
                                            <input type="radio" class="mask mask-star-2 bg-primary" 
                                                   checked disabled />
                                        @endfor
                                        @for($j = 0; $j < $drawerEmpty; $j++)
                                            <input type="radio" class="mask mask-star-2 bg-gray-300" 
                                                   disabled />
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <h3 class="font-bold">Allergènes du menu</h3>
                    <div class="flex flex-wrap gap-2 mt-2">
                        @php
                            $menuAllergens = $selectedMenu->products
                                ->pluck('allergens')
                                ->flatten()
                                ->unique()
                                ->filter();
                        @endphp
                        
                        @forelse($menuAllergens as $allergen)
                            <x-badge :value="$allergen" class="badge-outline badge-sm" />
                        @empty
                            <span class="text-sm text-gray-500">
                                Aucun allergène déclaré dans ce menu
                            </span>
                        @endforelse
                    </div>
                </div>
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
                        <div class="rating rating-md">
                            @php
                                $rating = $selectedProduct->reviews_avg_rating ?? 0;
                                $fullStars = floor($rating);
                                $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                            @endphp

                            <!-- Full Stars -->
                            @for ($i = 0; $i < $fullStars; $i++)
                                <input type="radio" name="drawer-product-rating-{{ $selectedProduct->id }}" 
                                       class="mask mask-star-2 bg-primary" checked disabled />
                            @endfor

                            <!-- Half Star -->
                            @if ($hasHalfStar)
                                <input type="radio" name="drawer-product-rating-{{ $selectedProduct->id }}" 
                                       class="mask mask-star-2 bg-primary mask-half-1" 
                                       checked disabled />
                                <input type="radio" name="drawer-product-rating-{{ $selectedProduct->id }}" 
                                       class="mask mask-star-2 bg-primary mask-half-2" 
                                       checked disabled />
                            @endif

                            <!-- Empty Stars -->
                            @for ($i = 0; $i < $emptyStars; $i++)
                                <input type="radio" name="drawer-product-rating-{{ $selectedProduct->id }}" 
                                       class="mask mask-star-2 bg-gray-300" disabled />
                            @endfor
                        </div>
                        <span class="text-gray-600">
                            ({{ $selectedProduct->reviews->count() }} avis)
                        </span>
                    </div>

                    <div class="space-y-4">
                        @foreach($selectedProduct->reviews->take(3) as $review)
                            <div class="bg-base-100 p-4 rounded-lg">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="font-bold">
                                        {{ $review->user->name }}
                                    </div>
                                    <div class="rating rating-xs">
                                        @php
                                            $drawerRating = $review->rating;
                                            $drawerFull = floor($drawerRating);
                                            $drawerEmpty = 5 - $drawerFull;
                                        @endphp

                                        @for($j = 0; $j < $drawerFull; $j++)
                                            <input type="radio" class="mask mask-star-2 bg-primary" 
                                                   checked disabled />
                                        @endfor
                                        @for($j = 0; $j < $drawerEmpty; $j++)
                                            <input type="radio" class="mask mask-star-2 bg-gray-300" 
                                                   disabled />
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    </div>
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
