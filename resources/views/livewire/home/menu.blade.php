<div class="py-16 ">
    <h2 class="text-3xl md:text-5xl font-bold text-center mb-8">Nos créations incontournables</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <x-card title="Californias Saumon-Avocat" subtitle="Saumon et avocat" shadow separator>
            <x-slot:figure>
                <img src="/Needs/California saumon-avocat-cheese.webp" alt="Californias Saumon-Avocat" />
            </x-slot:figure>
            <x-slot:menu>
                <x-button label="19.99" class="btn-primary" />
            </x-slot:menu>
        </x-card>
        <x-card title="Nigiri Saumon x2" subtitle="Tranches de saumon frais sur riz vinaigré" shadow separator>
            <x-slot:figure>
                <img src="/Needs/Nigiri saumon.webp" alt="Nigiri Saumon x2" />
            </x-slot:figure>
            <x-slot:menu>
                <x-button label="9.99" class="btn-primary" />
            </x-slot:menu>
        </x-card>
        <x-card title="Menu Mixte M" subtitle="3 nigiris, 6 makis, 6 californias, une boisson et un dessert" shadow separator>
            <x-slot:figure>
                <img src="Needs/Tiramisu-classique.webp" alt="Menu Mixte M" />
            </x-slot:figure>
            <x-slot:menu>
                <x-button label="39.99" class="btn-primary" />
            </x-slot:menu>
        </x-card>
    </div>
    <div class="text-center mt-8">
        <x-button link="/menu" label="Voir le Menu Complet" class="btn-secondary" />
    </div>
</div>