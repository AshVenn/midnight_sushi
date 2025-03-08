<!-- menu.blade.php -->
<div class="py-16 md:py-24 px-4" x-intersect="$el.classList.add('animate-fade-in')">
    <div class="max-w-7xl mx-auto space-y-12">
        <h2 class="text-3xl md:text-4xl font-bold text-center">
            Nos Menus <span class="text-primary">Phares</span>
        </h2>

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($featuredMenus as $menu)
                <div class="group relative bg-base-100 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="overflow-hidden rounded-t-2xl">
                        <img 
                            src="{{ $menu->image }}" 
                            alt="{{ $menu->name }}" 
                            class="w-full h-48 object-cover transform group-hover:scale-105 transition-all duration-300"
                        >
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <h3 class="text-xl font-bold">{{ $menu->name }}</h3>
                        
                        <div class="flex items-center gap-2">
                            <x-rating :value="$menu->rating" class="text-accent" />
                            <span class="text-sm text-gray-500">
                                ({{ $menu->reviews_count }} avis)
                            </span>
                        </div>

                        <p class="text-gray-600 line-clamp-3">{{ $menu->description }}</p>
                        
                        <div class="flex justify-between items-center">
                            <x-badge :value="number_format($menu->price, 2).' CHF'" class="badge-secondary" />
                            <x-button 
                                link="/menu/{{ $menu->slug }}" 
                                label="Découvrir" 
                                class="btn-ghost"
                            />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>