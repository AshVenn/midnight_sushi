<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Menu;
use App\Models\CustomMenu;
use App\Models\Review;
use App\Models\MenuTemplate;

class ReviewsSeeder extends Seeder
{
    public function run(): void
    {
        // On ne garde que les clients
        $customers = User::where('role', 'customer')->get();

        if ($customers->isEmpty()) {
            $this->command->warn('Aucun client trouvé – ReviewsSeeder ignoré.');
            return;
        }

        // Produits
        Product::all()->each(fn ($prod)  => $this->seedReviews($prod, $customers));

        // Menus fixes
        Menu::all()->each(fn ($menu)     => $this->seedReviews($menu, $customers));

        // Menus personnalisés
        MenuTemplate::all()->each(function ($tpl) use ($customers) {
            $this->createReviewsForItem($tpl, $customers);
        });


        $this->command->info('Avis générés (produits, menus & menus personnalisés) !');
    }

    /**
     * Crée un avis par client pour l’élément donné.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $item
     * @param  \Illuminate\Support\Collection       $customers
     */
    private function seedReviews($item, $customers): void
    {
        foreach ($customers as $customer) {
            Review::create([
                'user_id'         => $customer->id,
                'reviewable_id'   => $item->id,
                'reviewable_type' => match (true) {
                        $item instanceof Product      => 'Product',
                        $item instanceof Menu         => 'Menu',
                        $item instanceof MenuTemplate => 'MenuTemplate', // ← ajouté
                        default                       => get_class($item),
                        },
                'rating'          => 5,            // Variation 3★‑5★
            ]);
        }
    }
}
