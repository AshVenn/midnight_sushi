{{-- resources/views/livewire/menus-products.blade.php --}}
<div class="p-4 space-y-8" id="menuSection">

    {{-- ===== Barre de recherche & filtre allergène ===== --}}
    <div class="mb-4 lg:flex lg:items-center lg:justify-between lg:space-y-0 space-y-4">
        <x-input icon="o-magnifying-glass"
                 wire:model.live="searchTerm"
                 placeholder="Rechercher menus, produits, boissons…"
                 class="w-full lg:max-w-lg"/>

        <x-select wire:model.live="selectedAllergen"
                  :options="$allAllergens"
                  option-label="name"
                  option-value="id"
                  class="w-full md:w-64"
                  placeholder="Filtrer par allergène"/>
    </div>

    {{-- ====================== MENUS ====================== --}}
    <section class="space-y-4">
        <h2 class="text-2xl font-bold text-primary">Nos Menus</h2>

        <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            @forelse($menus as $menu)
                <x-card class="hover:shadow-lg transition">
                    <img src="{{ $menu->image }}"
                         alt="{{ $menu->name }}"
                         class="w-full h-48 bg-base-200 object-contain rounded-t-lg"
                         onerror="this.src='{{ asset('images/midnight_sushi/midnight_menu.jpg') }}'"/>

                    <div class="p-4 space-y-2">
                        <h3 class="text-xl font-semibold">{{ $menu->name }}</h3>

                        @include('partials.rating-badge', [
                            'rating' => $menu->reviews_avg_rating ?? 0,
                            'count'  => $menu->reviews->count()
                        ])

                        <div class="flex justify-between items-center">
                            <x-badge :value="number_format($menu->price,2).'CHF'" class="badge-primary"/>
                            <div class="flex gap-2">
                                <x-button icon="o-eye" class="btn-ghost"
                                          wire:click.prevent="showMenuDetails({{ $menu->id }})"/>
                                <x-button icon="o-plus" class="btn-primary"
                                          wire:click.prevent="addToCart('menu', {{ $menu->id }})"/>
                            </div>
                        </div>
                    </div>
                </x-card>
            @empty
                <x-alert icon="o-face-frown" class="col-span-full">Aucun menu trouvé</x-alert>
            @endforelse
        </div>

        {{ $menus->links() }}
    </section>

    {{-- ============ MENUS PERSONNALISABLES ============ --}}
    <section class="space-y-4">
        <h2 class="text-2xl font-bold text-primary">Menus personnalisables</h2>

        <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            @foreach($templates as $tpl)
                <x-card class="hover:shadow-lg transition">
                    <img src="{{ $tpl->image }}"
                         alt="{{ $tpl->name }}"
                         class="w-full h-48 bg-base-200 object-contain rounded-t-lg"
                         onerror="this.src='{{ asset('images/midnight_sushi/midnight_menu.jpg') }}'"/>

                    <div class="p-4 space-y-2">
                        <h3 class="text-xl font-semibold">{{ $tpl->name }}</h3>
                        <p class="line-clamp-2 text-gray-600">{{ $tpl->description }}</p>

                        <div class="flex justify-between items-center">
                            <x-badge :value="number_format($tpl->price,2).'CHF'" class="badge-warning"/>
                            <x-button label="Composer" icon="o-pencil"
                                      wire:click="buildMenu({{ $tpl->id }})"/>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        {{ $templates->links() }}
    </section>

    {{-- ====================== BOISSONS ====================== --}}
    <section class="space-y-4">
        <h2 class="text-2xl font-bold text-primary">Nos Boissons</h2>

        <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
            @forelse($drinks as $drink)
                <x-card class="hover:shadow-lg transition">
                    <img src="{{ $drink->image }}"
                         alt="{{ $drink->name }}"
                         class="w-full h-48 bg-base-200 object-contain rounded-t-lg"
                         onerror="this.src='{{ asset('images/midnight_sushi/midnight_boisson.jpg') }}'"/>

                    <div class="p-4 space-y-2">
                        <h3 class="text-xl font-semibold">{{ $drink->name }}</h3>

                        @include('partials.rating-badge', [
                            'rating' => $drink->reviews_avg_rating ?? 0,
                            'count'  => $drink->reviews->count()
                        ])

                        <div class="flex justify-between items-center">
                            <x-badge :value="number_format($drink->final_price,2).'CHF'"
                                     :class="'badge-'.($drink->isOnPromotion() ? 'warning' : 'secondary')"/>

                            <x-button icon="o-plus" class="btn-secondary"
                                      wire:click.prevent="addToCart('product', {{ $drink->id }})"/>
                        </div>
                    </div>
                </x-card>
            @empty
                <x-alert icon="o-face-frown" class="col-span-full">Aucune boisson trouvée</x-alert>
            @endforelse
        </div>

        {{ $drinks->links() }}
    </section>

    {{-- ===================== PRODUITS ===================== --}}
    <section class="space-y-4">
        <h2 class="text-2xl font-bold text-primary">Nos Produits</h2>

        <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-4">
            @forelse($products as $product)
                <x-card class="hover:shadow-lg transition">
                    <img src="{{ $product->image }}"
                         alt="{{ $product->name }}"
                         class="w-full h-48 bg-base-200 object-contain rounded-t-lg"
                         onerror="this.src='{{ asset('images/midnight_sushi/midnight_product.jpg') }}'"/>

                    <div class="p-4 space-y-2">
                        <h3 class="text-xl font-semibold">{{ $product->name }}</h3>

                        @include('partials.rating-badge', [
                            'rating' => $product->reviews_avg_rating ?? 0,
                            'count'  => $product->reviews->count()
                        ])

                        <p class="text-sm text-gray-600">{{ $product->category->name }}</p>

                        <div class="flex justify-between items-center">
                            <x-badge :value="number_format($product->final_price,2).'CHF'"
                                     :class="'badge-'.($product->isOnPromotion() ? 'warning' : 'secondary')"/>

                            <div class="flex gap-2">
                                <x-button icon="o-eye" class="btn-ghost"
                                          wire:click.prevent="showProductDetails({{ $product->id }})"/>
                                <x-button icon="o-plus" class="btn-secondary"
                                          wire:click.prevent="addToCart('product', {{ $product->id }})"/>
                            </div>
                        </div>
                    </div>
                </x-card>
            @empty
                <x-alert icon="o-face-frown" class="col-span-full">Aucun produit trouvé</x-alert>
            @endforelse
        </div>

        {{ $products->links() }}
    </section>

    {{-- ============ Drawer: détails Menu fixe ============ --}}
    <x-drawer wire:model="showMenuDrawer"
              class="w-11/12 lg:w-1/3"
              title="Détails du menu"
              right with-close-button separator>
        @if($selectedMenu)
            <div class="space-y-6">
                <img src="{{ $selectedMenu->image }}"
                     alt="{{ $selectedMenu->name }}"
                     class="w-full h-48 bg-base-200 object-contain rounded-t-lg"
                     onerror="this.src='{{ asset('images/midnight_sushi/midnight_menu.jpg') }}'"/>

                <x-badge :value="number_format($selectedMenu->price,2).'CHF'" class="badge-primary"/>

                <p>{{ $selectedMenu->description }}</p>

                <h3 class="font-bold">Contenu</h3>
                <ul class="space-y-2">
                    @foreach($selectedMenu->products as $p)
                        <li class="flex justify-between p-2 bg-base-200 rounded">
                            <span>{{ $p->name }}</span>
                            <span class="text-sm text-gray-600">{{ number_format($p->price,2) }}CHF</span>
                        </li>
                    @endforeach
                </ul>

                @include('partials.rating-badge', [
                    'rating' => $selectedMenu->reviews_avg_rating ?? 0,
                    'count'  => $selectedMenu->reviews->count()
                ])

                <x-slot:actions>
                    <x-button class="btn-ghost" label="Fermer"
                              @click="$wire.showMenuDrawer=false"/>
                    <x-button class="btn-primary" icon="o-shopping-cart"
                              label="Ajouter au panier"
                              wire:click="addToCart('menu', {{ $selectedMenu->id }})"/>
                </x-slot:actions>
            </div>
        @endif
    </x-drawer>

    {{-- =========== Drawer: détails Produit =========== --}}
    <x-drawer wire:model="showProductDrawer"
              class="w-11/12 lg:w-1/3"
              title="Détails du produit"
              right with-close-button separator>
        @if($selectedProduct)
            <div class="space-y-6">
                <img src="{{ $selectedProduct->image }}"
                     alt="{{ $selectedProduct->name }}"
                     class="w-full h-48 bg-base-200 object-contain rounded-lg"
                     onerror="this.src='{{ asset('images/midnight_sushi/midnight_product.jpg') }}'"/>

                <x-badge :value="number_format($selectedProduct->final_price,2).'CHF'"
                         :class="'badge-'.($selectedProduct->isOnPromotion() ? 'warning' : 'secondary')"/>

                <h3 class="font-bold">Catégorie</h3>
                <p>{{ $selectedProduct->category->name }}</p>

                <h3 class="font-bold">Allergènes</h3>
                <div class="flex flex-wrap gap-2">
                    @forelse($selectedProduct->allergens ?? [] as $a)
                        <x-badge :value="$a" class="badge-outline badge-sm"/>
                    @empty
                        <span class="text-sm text-gray-500">Aucun</span>
                    @endforelse
                </div>

                @include('partials.rating-badge', [
                    'rating' => $selectedProduct->reviews_avg_rating ?? 0,
                    'count'  => $selectedProduct->reviews->count()
                ])

                <x-slot:actions>
                    <x-button class="btn-ghost" label="Fermer"
                              @click="$wire.showProductDrawer=false"/>
                    <x-button class="btn-secondary" icon="o-shopping-cart"
                              label="Ajouter au panier"
                              wire:click="addToCart('product', {{ $selectedProduct->id }})"/>
                </x-slot:actions>
            </div>
        @endif
    </x-drawer>

    {{-- =========== Drawer: Builder Menu custom =========== --}}
    <x-drawer wire:model="showBuilderDrawer"
              class="w-11/12 lg:w-1/3"
              title="Composez votre menu"
              right with-close-button>
        @if($buildingTemplate)
            <h2 class="text-xl font-bold mb-4">{{ $buildingTemplate->name }}</h2>

            @foreach($buildingTemplate->categories as $cat)
                <div class="mb-6">
                    <h3 class="font-semibold">
                        {{ $cat->name }}
                        <span class="text-xs text-gray-500">
                            ({{ $cat->pivot->min_items }}–{{ $cat->pivot->max_items }})
                        </span>
                    </h3>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                        @foreach($cat->products as $prod)
                            <div class="border rounded-lg p-2 space-y-1">
                                <img src="{{ $prod->image }}"
                                     alt="{{ $prod->name }}"
                                     class="h-24 w-full bg-base-200 object-contain rounded"/>

                                <p class="text-sm font-semibold">{{ $prod->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ number_format($prod->price,2) }}CHF
                                </p>

                                {{-- badges allergènes --}}
                                <div class="flex flex-wrap gap-1 mb-1">
                                    @foreach($prod->allergens ?? [] as $a)
                                        <x-badge :value="$a" class="badge-outline badge-xs"/>
                                    @endforeach
                                </div>

                                <x-input type="number"
                                         min="0"
                                         max="{{ $cat->pivot->max_items }}"
                                         wire:model.lazy="templateSelections.{{ $cat->id }}.{{ $prod->id }}"
                                         class="w-full text-center"/>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            @error('templateSelections')
                <div class="text-error text-sm mb-3">{{ $message }}</div>
            @enderror

            <x-slot:actions>
                <x-button class="btn-ghost" label="Fermer"
                          @click="$wire.showBuilderDrawer=false"/>
                <x-button label="Ajouter au panier"
                          icon="o-shopping-cart"
                          wire:click="addCustomMenuToCart"/>
            </x-slot:actions>
        @endif
    </x-drawer>
</div>
