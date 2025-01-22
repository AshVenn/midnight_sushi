<div class="p-4" id="menuSection">
    <h2 class="text-2xl font-bold mb-4">Nos Menus</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 menu-grid">
        @foreach($menus as $menu)
            <!-- Removed 'opacity-0' and 'translate-y-10' classes -->
            <div class="border rounded-lg p-4 menu-item">
            <img
                src="{{ $menu->image }}"
                alt="{{ $menu->name }}"
                class="w-full h-48 object-cover mb-2 rounded"
                onerror="this.onerror=null; this.src='./images/sushi/midnight404.jpg';"
            />

                <h3 class="text-xl font-semibold">{{ $menu->name }}</h3>
                <p>{{ $menu->description }}</p>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-lg font-bold">
                        €{{ number_format($menu->price, 2) }}
                    </span>

                    <!-- Button: Add the menu to cart -->
                    <x-button
                        label="Ajouter" 
                        wire:click="addToCart('menu', {{ $menu->id }})"
                        class="btn-secondary"
                    />

                    <!-- Button: Details -->
                    <x-button
                        label="Détails"
                        wire:click="showMenuDetails({{ $menu->id }})"
                    />
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="text-2xl font-bold mt-8 mb-4">Nos Produits</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 product-grid">
        @foreach($products as $product)
            <!-- Removed 'opacity-0' and 'translate-y-10' classes -->
            <div class="border rounded-lg p-4 product-item">
                <img
                    src="{{ $product->image }}"
                    alt="{{ $product->name }}"
                    class="w-full h-48 object-cover mb-2 rounded"
                    onerror="this.onerror=null; this.src='./images/sushi/midnight404.jpg';"
                    />
                <h3 class="text-xl font-semibold">{{ $product->name }}</h3>
                <p>{{ $product->description }}</p>
                <p>Catégorie: {{ $product->category->name }}</p>
                <div class="flex justify-between items-center mt-2">
                    <span class="text-lg font-bold">
                        €{{ number_format($product->final_price, 2) }}
                    </span>

                    <!-- Button: Add to cart -->
                    <x-button
                        label="Ajouter"
                        wire:click="addToCart('product', {{ $product->id }})"
                    />
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Removed the entire animation script --}}
