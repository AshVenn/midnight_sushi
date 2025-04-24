{{-- PRESENTATION - image + texte en grille responsive --}}
<section class="py-16 lg:py-24 px-4" x-intersect="$el.classList.add('animate-fade-in')">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-8 md:gap-12 items-center">

        {{-- Texte --}}
        <div class="space-y-6 order-2 md:order-1">
            <h2 class="text-3xl md:text-4xl font-extrabold leading-snug">
                L'art du sushi<br>
                <span class="text-primary">réinventé pour la nuit</span>
            </h2>
            <p class="text-lg text-gray-700 dark:text-gray-300">
                Ingrédients ultra-frais, recettes signatures et livraison express de 23 h
                à 5 h - pour combler toutes vos fringales nocturnes.
            </p>

            <div class="flex flex-wrap gap-3">
                <x-button link="/menus-products"
                          label="Voir la carte"
                          class="btn-primary" />
                <x-button link="/about"
                          label="En savoir plus"
                          class="btn-ghost" />
            </div>
        </div>
        {{-- Visuel --}}
        <figure class="relative group order-1 md:order-2">
            <img  src="/images/sushi/chef.jpg"
                  alt="Chef préparant des sushis"
                  class="rounded-2xl shadow-lg w-full max-w-[500px] mx-auto aspect-[4/3] object-cover
                         group-hover:scale-[1.02] transition duration-300" />
            {{-- Overlay gradient pour lisibilité --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent rounded-2xl"></div>
        </figure>
    </div>
</section>