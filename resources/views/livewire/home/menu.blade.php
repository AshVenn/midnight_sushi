<div class="py-16 menu-section opacity-0 translate-y-10">
    <h2 class="text-3xl md:text-5xl font-bold text-center mb-8">Nos créations incontournables</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 menu-grid">
        {{-- Card 1 --}}
        <div class="menu-card opacity-0 translate-y-10 rotate-3 scale-95">
            <x-card title="Californias Saumon-Avocat" subtitle="Saumon et avocat" shadow separator>
                <x-slot:figure>
                    <img src="/Needs/California saumon-avocat-cheese.webp" alt="Californias Saumon-Avocat" class="w-full h-64 object-cover" />
                </x-slot:figure>
                <x-slot:menu>
                    <x-button label="19.99" class="btn-primary" />
                </x-slot:menu>
            </x-card>
        </div>

        {{-- Card 2 --}}
        <div class="menu-card opacity-0 translate-y-10 -rotate-3 scale-95">
            <x-card title="Nigiri Saumon x2" subtitle="Tranches de saumon frais sur riz vinaigré" shadow separator>
                <x-slot:figure>
                    <img src="/Needs/Nigiri saumon.webp" alt="Nigiri Saumon x2" class="w-full h-64 object-cover" />
                </x-slot:figure>
                <x-slot:menu>
                    <x-button label="9.99" class="btn-primary" />
                </x-slot:menu>
            </x-card>
        </div>

        {{-- Card 3 --}}
        <div class="menu-card opacity-0 translate-y-10 rotate-3 scale-95">
            <x-card title="Menu Mixte M" subtitle="3 nigiris, 6 makis, 6 californias, une boisson et un dessert" shadow separator>
                <x-slot:figure>
                    <img src="/Needs/Tiramisu-classique.webp" alt="Menu Mixte M" class="w-full h-64 object-cover" />
                </x-slot:figure>
                <x-slot:menu>
                    <x-button label="39.99" class="btn-primary" />
                </x-slot:menu>
            </x-card>
        </div>
    </div>

    <div class="text-center mt-8 opacity-0 translate-y-5 menu-button">
        <x-button link="/menus-products" label="Voir le Menu Complet" class="btn-secondary" />
    </div>
</div>

{{-- Animation Script --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const { animate, stagger } = Motion;

    // 🪄 Section Reveal Animation
    animate('.menu-section', 
        { opacity: [0, 1], translateY: [20, 0] }, 
        { duration: 1, easing: 'easeOut' }
    );

    // 🕺 Jiggy Card Entrance Animation
    const menuCards = document.querySelectorAll('.menu-card');
    animate(menuCards, 
        { 
            opacity: [0, 1], 
            translateY: [50, 0], 
            rotate: [5, 0],
            scale: [0.95, 1],
        }, 
        { 
            duration: 1, 
            delay: stagger(0.2), 
            easing: 'easeOut',
            repeat: 0,
        }
    );

    // 🎸 Jiggy Hover Effect
    menuCards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            animate(card, { 
                rotate: [0, -2, 2, 0], 
                scale: 1.05 
            }, { 
                duration: 0.5, 
                easing: 'easeInOut' 
            });
        });
        card.addEventListener('mouseleave', () => {
            animate(card, { 
                rotate: 0, 
                scale: 1 
            }, { 
                duration: 0.3, 
                easing: 'easeOut' 
            });
        });
    });

    // 🛎️ Button Entrance Animation
    animate('.menu-button', 
        { opacity: [0, 1], translateY: [20, 0], scale: [0.9, 1.1, 1] }, 
        { delay: 1.5, duration: 0.8, easing: 'easeOut' }
    );
});
</script>
