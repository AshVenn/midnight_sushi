<div>
    {{-- Show the modal only if $menu is not null --}}
    @if ($menu)
        {{-- Use WireUI's <x-modal> syntax --}}
        <x-modal 
            wire:model="isModalOpen" 
            title="{{ $menu->name }}" 
            maxWidth="lg"
            {{-- 
               (Optional) you can add classes if you want blur or other styling:
               class="backdrop-blur" 
               box-class="bg-white p-6 w-full md:w-1/2 rounded"
            --}}
        >
            {{-- Modal content in a <div> --}}
            <div class="space-y-4">
                {{-- Menu image --}}
                <img
                    src="{{ $menu->image }}"
                    alt="{{ $menu->name }}"
                    class="w-full h-64 object-cover rounded"
                />

                {{-- Description --}}
                <p>
                    {{ $menu->description }}
                </p>

                {{-- List of products --}}
                <h3 class="text-xl font-semibold mt-4">
                    Contenu du Menu
                </h3>
                <ul class="list-disc list-inside">
                    @foreach($menu->products as $product)
                        <li>
                            {{ $product->name }}
                            - €{{ number_format($product->discount_price ?? $product->price, 2) }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Actions slot (footer) --}}
            <x-slot:actions>
                <x-button
                    label="Ajouter au Panier"
                    wire:click="addToCart('menu', {{ $menu->id }})"
                    class="btn-primary"
                />
                <x-button
                    label="Fermer"
                    @click="$wire.isModalOpen = false"
                    
                />
            </x-slot:actions>
        </x-modal>
    @endif
</div>
