<div class="relative bg-cover bg-center h-screen" style="background-image: url('/images/sushi/hero-bg.jpg');">
    <div class="absolute inset-0"></div>
    <div class="relative z-10 text-center flex flex-col items-center justify-center h-full space-y-4 px-4">
        <h1 class="text-4xl md:text-6xl font-bold ">Midnight Sushi</h1>
        <p class="text-lg md:text-2xl ">Le plaisir nocturne du sushi, livré chez vous.</p>
        <div class="flex space-x-4 items-center">
            <x-button link="/menu" label="Découvrez notre Menu" class="btn-primary hidden md:inline-block text-center" />
            <x-button link="/order" label="Commandez Maintenant" class="btn-secondary hidden md:inline-block text-center" />
            <x-button icon="o-arrow-left" link="/menu" class="btn-primary md:hidden bg-transparent text-black dark:text-white" />
            <x-button icon="o-shopping-cart" link="/order" class="btn-secondary md:hidden bg-transparent text-black dark:text-white" />
        </div>
    </div>
</div>