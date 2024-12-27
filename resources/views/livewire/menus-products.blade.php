<div class="p-4" id="menuSection">
    <h2 class="text-2xl font-bold mb-4">Nos Menus</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 menu-grid">
        @foreach($menus as $menu)
            <div class="border rounded-lg p-4 menu-item opacity-0 translate-y-10">
                <img
                    src="{{ $menu->image }}"
                    alt="{{ $menu->name }}"
                    class="w-full h-48 object-cover mb-2 rounded"
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
            <div class="border rounded-lg p-4 product-item opacity-0 translate-y-10">
                <img
                    src="{{ $product->image }}"
                    alt="{{ $product->name }}"
                    class="w-full h-48 object-cover mb-2 rounded"
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

{{-- Animation Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const { animate, stagger } = Motion;

    // SECTION ENTRANCE (Slightly faster, a bit bouncy)
    animate('#menuSection', 
        { 
            opacity: [0, 1], 
            translateY: [20, 0],
            scale: [0.95, 1.03, 1], // quick jiggle
        }, 
        { 
            duration: 0.6, 
            easing: 'easeOut',
        }
    );

    // MENU CARDS ENTRANCE
    const menuCards = document.querySelectorAll('.menu-item');
    animate(menuCards, 
        { 
            // Multiple keyframes for a jiggle
            opacity: [0, 1],
            translateY: [60, 0],
            scale: [0.8, 1.1, 0.98, 1], 
        }, 
        { 
            duration: 0.6, 
            delay: stagger(0.1), 
            easing: 'easeInOut',
        }
    );

    // PRODUCT CARDS ENTRANCE
    const productCards = document.querySelectorAll('.product-item');
    animate(productCards, 
        { 
            opacity: [0, 1],
            translateY: [60, 0],
            scale: [0.8, 1.1, 0.98, 1],
        }, 
        { 
            duration: 0.6,
            delay: stagger(0.1),
            easing: 'easeInOut',
        }
    );

    // HOVER WIGGLE
    [...menuCards, ...productCards].forEach(card => {
        card.addEventListener('mouseenter', () => {
            animate(card, 
                {
                    // Quick "jiggle" on hover
                    scale: [1, 1.05, 0.97, 1],
                    rotate: [0, 2, -2, 0],
                }, 
                { duration: 0.4, easing: 'easeInOut' }
            );
        });
        card.addEventListener('mouseleave', () => {
            // Return to normal
            animate(card, 
                {
                    scale: 1,
                    rotate: 0,
                }, 
                { duration: 0.3, easing: 'easeOut' }
            );
        });
    });
});
</script>
