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
                        
                        <!-- Updated SVG Star Rating -->
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
                            <span class="text-sm text-gray-500">
                                ({{ $menu->reviews_count }} avis)
                            </span>
                        </div>

                        <p class="text-gray-600 line-clamp-3">{{ $menu->description }}</p>
                        
                        <div class="flex justify-between items-center">
                            <x-badge :value="number_format($menu->price, 2).' CHF'" class="badge-secondary" />
                            <x-button 
                                link="/menus-products" 
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