<div>
    <!-- HEADER -->
    <header class="shadow p-4 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Midnight Sushi</h1>
        <div class="flex items-center space-x-4">
            <input type="text" placeholder="Search for sushi..." class="border rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300" />
            <button class="px-4 py-2 rounded bg-primary hover:bg-primary-focus text-secondary-content">Filters</button>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative p-8 text-center">
        <h1 class="text-4xl font-bold">Welcome to Midnight Sushi</h1>
        <p class="text-lg mt-4">
            Experience unique and fresh sushi flavors, made with premium ingredients and served late into the night.
        </p>
        <button class="bg-primary px-6 py-3 rounded mt-6 text-secondary-content hover:bg-primary-focus">
            Explore Our Menu
        </button>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section class="mt-8 mx-auto max-w-4xl p-4">
        <h2 class="text-2xl font-bold mb-4">Our Specialties</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Product Card -->
            <div class="shadow rounded p-4 bg-base-100">
                <img src="https://via.placeholder.com/300" alt="Dragon Roll" class="w-full h-48 object-cover rounded mb-4">
                <h3 class="text-lg font-bold">Dragon Roll</h3>
                <p>Tempura shrimp, avocado, spicy mayo</p>
                <p class="text-lg font-bold mt-2">€12.00</p>
                <button class="px-4 py-2 rounded mt-4 bg-primary text-secondary-content hover:bg-primary-focus">
                    Add to Cart
                </button>
            </div>
            <!-- Repeat for more products -->
            <div class="shadow rounded p-4 bg-base-100">
                <img src="https://via.placeholder.com/300" alt="Rainbow Roll" class="w-full h-48 object-cover rounded mb-4">
                <h3 class="text-lg font-bold">Rainbow Roll</h3>
                <p>Tuna, salmon, avocado, tobiko</p>
                <p class="text-lg font-bold mt-2">€14.00</p>
                <button class="px-4 py-2 rounded mt-4 bg-primary text-secondary-content hover:bg-primary-focus">
                    Add to Cart
                </button>
            </div>
            <div class="shadow rounded p-4 bg-base-100">
                <img src="https://via.placeholder.com/300" alt="Vegan Delight" class="w-full h-48 object-cover rounded mb-4">
                <h3 class="text-lg font-bold">Vegan Delight</h3>
                <p>Cucumber, carrot, marinated tofu</p>
                <p class="text-lg font-bold mt-2">€10.00</p>
                <button class="px-4 py-2 rounded mt-4 bg-primary text-secondary-content hover:bg-primary-focus">
                    Add to Cart
                </button>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="py-6 text-center bg-base-200">
        <p>&copy; 2024 Midnight Sushi | All rights reserved.</p>
        <p>Contact: +33 1 23 45 67 89 | Address: 123 Night Street, Paris</p>
        <div class="flex justify-center mt-4 space-x-4">
            <a href="#" class="hover:underline">Facebook</a>
            <a href="#" class="hover:underline">Instagram</a>
            <a href="#" class="hover:underline">Twitter</a>
        </div>
    </footer>
</div>
