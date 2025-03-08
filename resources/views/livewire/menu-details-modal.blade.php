<x-modal wire:model="show" :title="$menu ? $menu->name : 'Menu'" class="backdrop-blur" title-class="text-xl font-bold">

    @if($menu)
        <x-badge :value="number_format($menu->price, 2).' CHF'" class="badge-primary ml-2" />
        <div class="space-y-6">
            <img 
                src="{{ $menu->image }}" 
                alt="{{ $menu->name }}"
                class="w-full h-48 object-cover rounded-lg"
                onerror="this.src='{{ asset('images/placeholder.jpg') }}'"
            />

            <div class="space-y-2">
                <h4 class="font-semibold text-lg">Description</h4>
                <p class="text-gray-600">{{ $menu->description }}</p>
            </div>

            <div class="space-y-2">
                <h4 class="font-semibold text-lg">Contenu du menu</h4>
                <ul class="space-y-2">
                    @foreach($menu->products as $product)
                        <li class="flex justify-between items-center p-2 bg-base-200 rounded">
                            <span>{{ $product->name }}</span>
                            <span class="text-sm text-gray-600">
                                {{ number_format($product->price, 2) }} CHF
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="space-y-2">
                <h4 class="font-semibold text-lg">Avis</h4>
                @if($menu->reviews->isEmpty())
                    <p class="text-gray-600">Aucun avis pour ce menu</p>
                @else
                    <div class="space-y-4">
                        <div class="flex items-center gap-2">
                            <x-rating :value="$menu->reviews->avg('rating')" class="text-warning text-lg" />
                            <span class="text-gray-600">
                                {{ $menu->reviews->count() }} avis
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <x-slot:actions>
            <x-button label="Fermer" @click="$wire.close()" class="btn-ghost" />
            <x-button 
                label="Ajouter au panier" 
                icon="o-shopping-cart" 
                wire:click="addToCart('menu', {{ $menu->id }})" 
                class="btn-primary" 
            />
        </x-slot:actions>
    @endif
</x-modal>