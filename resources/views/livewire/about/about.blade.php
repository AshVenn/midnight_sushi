<div>
    <!-- Hero Section -->
    <section class="flex flex-col justify-center items-center text-center overflow-hidden hero-section">
        <div class="max-w-2xl hero-content">
            <x-card shadow separator>
                <h2 class="text-4xl font-bold md:text-6xl">À propos de Midnight Sushi</h2>
                <p class="mt-4 text-lg md:text-xl">Un concept unique pour les amateurs de sushi nocturnes. Nous sommes ouvert de 23h à 5h.</p>
            </x-card>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-20 px-6 mission-section">
        <div class="max-w-5xl mx-auto">
            <x-card shadow separator class="mission-main-card">
                <h3 class="text-2xl font-bold mb-4">Notre mission</h3>
                <p class="text-lg leading-relaxed">
                    Chez Midnight Sushi, notre mission est simple : vous offrir des sushis de la plus haute qualité, préparés avec soin et disponibles aux moments où vous en avez le plus envie.
                </p>
            </x-card>
            <ul class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <x-card shadow class="mission-card">
                    <h4 class="font-bold text-lg">Utiliser des ingrédients frais</h4>
                    <p class="mt-2">Chaque ingrédient est soigneusement choisi pour une qualité irréprochable.</p>
                </x-card>
                <x-card shadow class="mission-card">
                    <h4 class="font-bold text-lg">Assurer une expérience fluide</h4>
                    <p class="mt-2">Commande rapide grâce à notre plateforme intuitive.</p>
                </x-card>
                <x-card shadow class="mission-card">
                    <h4 class="font-bold text-lg">Réinventer les saveurs</h4>
                    <p class="mt-2">Des sushis modernes et uniques pour éveiller vos papilles.</p>
                </x-card>
            </ul>
        </div>
    </section>

    <!-- Parallax Section -->
    <section class="bg-fixed bg-cover bg-center bg-gray-200 relative parallax-section">
        <div class="absolute inset-0 flex justify-center items-center bg-black bg-opacity-50 parallax-content">
            <h3 class="text-4xl font-bold text-white">Découvrez nos saveurs inégalées</h3>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20 px-6 contact-section">
        <div class="max-w-5xl mx-auto contact-content">
            <x-card shadow separator>
                <h3 class="text-3xl font-bold mb-6">Prêts à découvrir Midnight Sushi ?</h3>
                <p class="text-lg mb-6 leading-relaxed">
                    Plongez dans un univers culinaire hors du commun et laissez-nous éveiller vos papilles. Consultez notre menu et commandez dès maintenant pour vivre l’expérience Midnight Sushi.
                </p>
                <x-button label="Découvrez notre menu" class="btn-primary contact-button" />
            </x-card>
        </div>
    </section>
</div>

{{-- Include after the page loads --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const { animate } = Motion;

    // Hero Section: Fade in and slide down
    animate('.hero-content', { opacity: [0, 1], y: [-20, 0] }, { duration: 0.8, easing: 'ease-out' });

    // Mission Main Card: Fade in
    animate('.mission-main-card', { opacity: [0, 1], y: [20,0] }, { delay: 0.2, duration: 0.7, easing: 'ease-out' });

    // Mission Cards: Staggered scale in
    const missionCards = document.querySelectorAll('.mission-card');
    missionCards.forEach((card, index) => {
        animate(card, { opacity: [0,1], scale: [0.95,1] }, { delay: 0.3 + index * 0.1, duration: 0.5, easing: 'ease-out' });
    });

    // Parallax Section Title: Fade in
    animate('.parallax-content', { opacity: [0,1] }, { delay: 0.5, duration: 1, easing: 'ease-in-out' });

    // Contact Section: Fade in and slide up
    animate('.contact-content', { opacity: [0,1], y: [30,0] }, { delay: 0.5, duration: 0.8, easing: 'ease-out' });
    animate('.contact-button', { opacity: [0,1], y: [10,0] }, { delay: 0.7, duration: 0.5, easing: 'ease-out' });
});
</script>
