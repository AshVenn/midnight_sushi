<x-card title="Détails du Menu">
    <div class="space-y-2">
        <div>
            <strong>Nom :</strong> {{ $menu->name }}
        </div>
        <div>
            <strong>Slug :</strong> {{ $menu->slug }}
        </div>
        <div>
            <strong>Description :</strong>
            {{ $menu->description ?: 'Aucune description' }}
        </div>
        <div>
            <strong>Prix :</strong>
            {{ number_format($menu->price, 2) }} €
        </div>
        <div>
            <strong>Prix Promo :</strong>
            @if($menu->discount_price)
                {{ number_format($menu->discount_price, 2) }} €
                @if($menu->isOnPromotion())
                    <span class="text-green-600 text-sm">(En Promotion Maintenant)</span>
                @else
                    <span class="text-orange-500 text-sm">(Période Expirée ou à Venir)</span>
                @endif
            @else
                <span class="text-gray-400">N/A</span>
            @endif
        </div>
        <div>
            <strong>Période Promo :</strong>
            @if($menu->promotion_start && $menu->promotion_end)
                du {{ $menu->promotion_start }} au {{ $menu->promotion_end }}
            @else
                <span class="text-gray-400">Non définie</span>
            @endif
        </div>
        <div class="mt-4">
            <strong>Image :</strong><br>
            @if($menu->image)
                <img src="{{ $menu->image }}" alt="{{ $menu->name }}" class="h-40 w-40 object-cover rounded-lg">
            @else
                <img src="/empty-image.jpg" alt="Aucune image" class="h-40 w-40 object-cover rounded-lg">
            @endif
        </div>
    </div>

    <hr class="my-4">

    <h2 class="text-lg font-semibold mb-2">Produits du Menu :</h2>
    @if($menu->products->count())
        <ul class="space-y-2">
            @foreach($menu->products as $product)
                <li class="border p-2 rounded">
                    <div>
                        <strong>{{ $product->name }}</strong> 
                        <span class="text-xs text-gray-500">(Slug: {{ $product->slug }})</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        Quantité : {{ $product->pivot->quantity }}
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-500">Aucun produit associé à ce menu.</p>
    @endif

    <div class="mt-4 flex space-x-2">
        <x-button label="Retour à la Liste" link="{{ route('manager.menus.index') }}" />
        <x-button label="Modifier" link="{{ route('manager.menus.edit', $menu) }}" class="btn-primary" />
    </div>
</x-card>
