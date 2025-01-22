<div class="relative md:h-screen flex flex-col items-center text-neutral dark:text-accent">
    <!-- Background Decorations (hidden on small) -->
    <div class="hidden md:block absolute top-0 left-0 w-full">
        <!-- Left Fish -->
        <img 
            src="/images/sushi/sushiFish.svg" 
            alt="Fish Decoration Left" 
            class="absolute top-0 left-6 w-12 md:w-20 lg:w-28"
        />
        <!-- Right Fish -->
        <img 
            src="/images/sushi/sushiFish.svg" 
            alt="Fish Decoration Right" 
            class="absolute top-0 right-6 w-12 md:w-20 lg:w-28 transform rotate-180"
        />
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 text-center px-6 md:px-12 max-w-screen-lg mt-0 md:mt-[-8rem] md:flex md:flex-col md:justify-center md:h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center space-x-4">
            <img 
                src="/Needs/Logo midnight sushi Light.svg" 
                alt="Midnight Sushi Logo Light" 
                class="dark:hidden w-64 md:w-[26rem] lg:w-[30rem]"
            />
            <img 
                src="/Needs/Logo midnight sushi Dark.svg" 
                alt="Midnight Sushi Logo Dark" 
                class="hidden dark:block w-64 md:w-[26rem] lg:w-[30rem]"
            />
        </div>

        <!-- Tagline -->
        <p class="mt-2 text-lg md:text-xl lg:text-2xl font-semibold">
            Le plaisir nocturne du sushi, livré chez vous.
        </p>

        <!-- Small Add-on Text -->
        <p class="mt-1 text-sm md:text-base text-gray-500 dark:text-gray-400 italic">
            Faites l'expérience de la qualité et de la fraîcheur.
        </p>

        <!-- Call-to-Action Buttons -->
        <div class="mt-4 flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
            <x-button 
                link="/menus-products" 
                label="Découvrez notre Menu" 
                class="btn-primary text-center px-6 py-3"
            />
            <x-button 
                link="/order" 
                label="Commandez Maintenant" 
                class="btn-secondary text-center px-6 py-3"
            />
        </div>
    </div>

    <!-- Sushi Plates with Scroll Effect -->
    <div id="plate-left" class="fixed bottom-0 left-0">
        <img 
            src="/images/sushi/sushiPlate.png" 
            alt="Sushi Plate Left" 
            class="w-40 md:w-56 lg:w-64 transform scale-x-[-1]"
        />
    </div>
    <div id="plate-right" class="fixed bottom-0 right-0">
        <img 
            src="/images/sushi/sushiPlate.png" 
            alt="Sushi Plate Right" 
            class="w-40 md:w-56 lg:w-64"
        />
    </div>
</div>

<script>
    document.addEventListener('scroll', () => {
        const scrollY = window.scrollY;

        // Left plate moves further left
        const leftPlate = document.getElementById('plate-left');
        if (leftPlate) {
            leftPlate.style.transform = `translateX(${-scrollY}px)`;
        }

        // Right plate moves further right
        const rightPlate = document.getElementById('plate-right');
        if (rightPlate) {
            rightPlate.style.transform = `translateX(${scrollY}px)`;
        }
    });
</script>
