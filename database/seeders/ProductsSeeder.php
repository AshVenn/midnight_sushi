<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // Get or create categories
        $categories = [
            'Californias' => Category::firstOrCreate(['name' => 'Californias'], ['slug' => 'californias'])->id,
            'Makis' => Category::firstOrCreate(['name' => 'Makis'], ['slug' => 'makis'])->id,
            'Nigiris' => Category::firstOrCreate(['name' => 'Nigiris'], ['slug' => 'nigiris'])->id,
            'Desserts' => Category::firstOrCreate(['name' => 'Desserts'], ['slug' => 'desserts'])->id,
            'Condiments' => Category::firstOrCreate(['name' => 'Condiments'], ['slug' => 'condiments'])->id,
        ];

        $products = [
            // Californias
            [
                'name' => 'Californias Saumon-Avocat',
                'price' => 19.00,
                'description' => 'Saumon et avocat',
                'category_id' => $categories['Californias'],
                'image' => 'images/midnight_sushi/californias-saumon-avocat.jpg',
                'allergens' => ['Poisson', 'Gluten']
            ],
            [
                'name' => 'Californias Saumon-Avocat Cheese',
                'price' => 12.00,
                'description' => 'Saumon, avocat, fromage frais',
                'category_id' => $categories['Californias'],
                'image' => 'images/midnight_sushi/californias-saumon-avocat-cheese.jpg',
                'allergens' => ['Poisson', 'Lait', 'Gluten']
            ],

            // Makis
            [
                'name' => 'Maki Saumon x8',
                'price' => 12.00,
                'description' => 'Rouleaux classiques au saumon',
                'category_id' => $categories['Makis'],
                'image' => 'images/midnight_sushi/maki-saumon-x8.jpg',
                'allergens' => ['Poisson', 'Gluten']
            ],
            [
                'name' => 'Maki Thon x8',
                'price' => 12.00,
                'description' => 'Rouleaux classiques au thon',
                'category_id' => $categories['Makis'],
                'image' => 'images/midnight_sushi/maki-thon-x8.jpg',
                'allergens' => ['Poisson', 'Gluten']
            ],
            [
                'name' => 'Maki Saumon Avocat',
                'price' => 14.00,
                'description' => 'Maki au saumon frais et avocat',
                'category_id' => $categories['Makis'],
                'image' => 'images/midnight_sushi/maki-saumon-avocat.jpg',
                'allergens' => ['Poisson', 'Gluten']
            ],
            [
                'name' => 'Maki Crevette Avocat',
                'price' => 15.00,
                'description' => 'Maki aux crevettes et avocat',
                'category_id' => $categories['Makis'],
                'image' => 'images/midnight_sushi/maki-crevette-avocat.jpg',
                'allergens' => ['Crustacés', 'Gluten']
            ],

            // Nigiris
            [
                'name' => 'Nigiri Saumon x2',
                'price' => 10.00,
                'description' => 'Tranches de saumon frais sur riz vinaigré',
                'category_id' => $categories['Nigiris'],
                'image' => 'images/midnight_sushi/nigiri-saumon-x2.jpg',
                'allergens' => ['Poisson']
            ],
            [
                'name' => 'Nigiri Crevette x2',
                'price' => 10.00,
                'description' => 'Crevette sur riz vinaigré',
                'category_id' => $categories['Nigiris'],
                'image' => 'images/midnight_sushi/nigiri-crevette-x2.jpg',
                'allergens' => ['Crustacés']
            ],

            // Desserts
            [
                'name' => 'Tiramisu Speculos Maison',
                'price' => 6.00,
                'description' => 'Une touche sucrée pour terminer votre repas',
                'category_id' => $categories['Desserts'],
                'image' => 'images/midnight_sushi/tiramisu-speculos-maison.jpg',
                'allergens' => ['Gluten', 'Lait', 'Œufs']
            ],
            [
                'name' => 'Cookie aux Trois Pépites',
                'price' => 7.00,
                'description' => 'Trois chocolats : noir, blanc et lait',
                'category_id' => $categories['Desserts'],
                'image' => 'images/midnight_sushi/cookie-trois-pepites.jpg',
                'allergens' => ['Gluten', 'Lait', 'Soja']
            ],

            // Condiments
            [
                'name' => 'Gingembre mariné',
                'price' => 3.00,
                'description' => 'Gingembre mariné accompagnement',
                'category_id' => $categories['Condiments'],
                'image' => 'images/midnight_sushi/gingembre-marine.jpg',
                'allergens' => ['Sulfites']
            ],
            [
                'name' => 'Wasabi',
                'price' => 2.50,
                'description' => 'Pâte de wasabi traditionnelle',
                'category_id' => $categories['Condiments'],
                'image' => 'images/midnight_sushi/wasabi.jpg',
                'allergens' => ['Moutarde']
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'slug' => \Str::slug($product['name']),
                'price' => $product['price'],
                'description' => $product['description'],
                'category_id' => $product['category_id'],
                'image' => $product['image'],
                'allergens' => $product['allergens'],
                'is_available' => true
            ]);
        }
    }
}