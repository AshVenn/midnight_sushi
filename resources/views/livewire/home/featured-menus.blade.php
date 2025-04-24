{{--  Menu Phares (+ menus personnalisables)  --}}
@php
    // On fusionne “menus phares” classiques et menus personnalisables en une seule collection
    $cards = collect($featuredMenus)
             ->merge($featuredCustomMenus ?? [])
             ->take(6);   // on limite visuellement à 6 cartes, mais ajustez selon vos besoins
@endphp

<div class="py-16 lg:py-24 px-4" x-intersect="$el.classList.add('animate-fade-in')">
    <div class="max-w-7xl mx-auto space-y-12">
        <!-- Titre -->
        <h2 class="text-3xl md:text-4xl font-bold text-center">
            Nos Menus <span class="text-primary">phares</span>
        </h2>

        <!-- Grille responsive -->
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($cards as $menu)
                <article
                    class="group relative bg-base-100 rounded-3xl shadow transition
                           hover:shadow-xl focus-within:ring-2 focus-within:ring-primary
                           ring-offset-2 dark:ring-offset-base-100">
                    
                    <!-- Badge “personnalisable” le cas échéant -->
                    @isset($menu->categories) {{-- ⇒ c’est un MenuTemplate --}}
                        <span class="absolute top-3 left-3 z-10 px-2 py-1 text-xs font-semibold uppercase
                                     rounded-full bg-primary text-white tracking-wider shadow">
                            Personnalisable
                        </span>
                    @endisset

                    <!-- Image -->
                    <div class="overflow-hidden rounded-t-3xl bg-neutral/5">
                        <img  src="{{ $menu->image }}"
                              alt="{{ $menu->name }}"
                              class="w-full aspect-[4/3] object-contain transition-transform duration-300
                                     group-hover:scale-105" />
                    </div>

                    <!-- Contenu -->
                    <div class="p-6 space-y-4">
                        <h3 class="text-xl font-bold leading-tight">
                            {{ $menu->name }}
                        </h3>

                        <!-- Stars -->
                        @include('partials.rating-badge', [
                            'rating' => $menu->reviews_avg_rating ?? 0,
                            'count'  => $menu->reviews->count()
                        ])

                        <!-- Description courte -->
                        <p class="text-sm md:text-base text-gray-600 dark:text-gray-400 line-clamp-3">
                            {{ $menu->description }}
                        </p>

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-2">
                            <x-badge :value="number_format($menu->price, 2).' CHF'"
                                     class="badge-secondary text-sm md:text-base" />

                            <x-button  link="/menus-products"
                                       label="Découvrir"
                                       class="btn-ghost btn-sm md:btn-md" />
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</div>
