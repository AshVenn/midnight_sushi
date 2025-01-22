<div>
    @if ($menu)
        <x-modal 
            wire:model="isModalOpen" 
            title="{{ $menu->name }}" 
            maxWidth="lg"
            box-class="p-4 sm:p-6 w-full md:w-3/4 lg:w-1/2 rounded max-h-screen overflow-y-auto"
            class="fixed inset-0 flex items-center justify-center"
        >
            {{-- Modal content --}}
            <div class="space-y-4">
                {{-- Menu image --}}
                <img
                    src="{{ $menu->image }}"
                    alt="{{ $menu->name }}"
                    class="w-full h-48 sm:h-64 object-cover rounded"
                    onerror="this.onerror=null; this.src='./images/sushi/midnight404.jpg';"
                />

                {{-- Description --}}
                <p class="text-sm sm:text-base leading-relaxed">
                    {{ $menu->description }}
                </p>

                {{-- List of products --}}
                <h3 class="text-lg sm:text-xl font-semibold mt-4">
                    Contenu du Menu
                </h3>
                <ul class="list-disc list-inside text-sm sm:text-base">
                    @foreach($menu->products as $product)
                        <li>
                            {{ $product->name }} - €{{ number_format($product->discount_price ?? $product->price, 2) }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Footer actions --}}
            <x-slot:actions>
                <x-button
                    label="Fermer"
                    wire:click="closeModal"
                    class="btn-btn-ghost"
                />
            </x-slot:actions>
        </x-modal>
    @endif
</div>
