<div>
    <!-- Hero Section -->
    <section class="flex flex-col justify-center items-center text-center overflow-hidden">
        <div class="max-w-2xl animate-fade-in-down">
            <x-card shadow separator>
                <h2 class="text-4xl font-bold md:text-6xl">À propos de Midnight Sushi</h2>
                <p class="mt-4 text-lg md:text-xl">Un concept unique pour les amateurs de sushi nocturnes. Nous sommes ouvert de 23h à 5h.</p>
            </x-card>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-20 px-6">
        <div class="max-w-5xl mx-auto">
            <x-card shadow separator>
                <h3 class="text-2xl font-bold mb-4">Notre mission</h3>
                <p class="text-lg leading-relaxed">
                    Chez Midnight Sushi, notre mission est simple : vous offrir des sushis de la plus haute qualité, préparés avec soin et disponibles aux moments où vous en avez le plus envie.
                </p>
            </x-card>
            <ul class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <x-card shadow>
                    <h4 class="font-bold text-lg ">Utiliser des ingrédients frais</h4>
                    <p class="mt-2">Chaque ingrédient est soigneusement choisi pour une qualité irréprochable.</p>
                </x-card>
                <x-card shadow>
                    <h4 class="font-bold text-lg">Assurer une expérience fluide</h4>
                    <p class="mt-2">Commande rapide grâce à notre plateforme intuitive.</p>
                </x-card>
                <x-card shadow>
                    <h4 class="font-bold text-lg">Réinventer les saveurs</h4>
                    <p class="mt-2">Des sushis modernes et uniques pour éveiller vos papilles.</p>
                </x-card>
            </ul>
        </div>
    </section>

    <!-- Parallax Section -->
    <section class=" bg-fixed bg-cover bg-center bg-gray-200">
        <div class="absolute inset-0 flex justify-center items-center bg-black bg-opacity-50 animate-fade-in">
            <h3 class="text-4xl font-bold text-white">Découvrez nos saveurs inégalées</h3>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="py-20 px-6">
        <div class="max-w-5xl mx-auto">
            <x-card shadow separator>
                <h3 class="text-3xl font-bold mb-6">Prêts à découvrir Midnight Sushi ?</h3>
                <p class="text-lg mb-6 leading-relaxed">
                    Plongez dans un univers culinaire hors du commun et laissez-nous éveiller vos papilles. Consultez notre menu et commandez dès maintenant pour vivre l’expérience Midnight Sushi.
                </p>
                <x-button label="Découvrez notre menu" class="btn-primary" />
            </x-card>
        </div>
    </section>
</div>
