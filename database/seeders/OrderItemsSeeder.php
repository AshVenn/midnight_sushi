<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\Menu;
use App\Models\CustomMenu;
use App\Models\OrderItem;

class OrderItemsSeeder extends Seeder
{
    /**
     * Seed the order_items table – produits, menus fixes
     * et menus personnalisés (CustomMenu).
     */
    public function run(): void
    {
        $orders       = Order::all();
        $products     = Product::all();
        $menus        = Menu::all();
        $customMenus  = CustomMenu::all();

        if (
            $orders->isEmpty() ||
            ($products->isEmpty() && $menus->isEmpty() && $customMenus->isEmpty())
        ) {
            $this->command->warn(
                'No Orders, Products, Menus or CustomMenus found. Skipping OrderItemsSeeder.'
            );
            return;
        }

        foreach ($orders as $order) {
            $itemsCount = rand(1, 5);

            for ($i = 0; $i < $itemsCount; $i++) {

                /* Détermine dynamiquement les types disponibles
                   afin d’éviter toute erreur si une collection est vide. */
                $pool = [];
                if ($products->isNotEmpty())    $pool[] = 'product';
                if ($menus->isNotEmpty())       $pool[] = 'menu';
                if ($customMenus->isNotEmpty()) $pool[] = 'custom_menu';

                if (empty($pool)) {
                    continue; // plus rien à piocher
                }

                $choice = collect($pool)->random();

                switch ($choice) {
                    case 'product':
                        $orderable     = $products->random();
                        $orderableType = 'App\\Models\\Product';
                        break;

                    case 'menu':
                        $orderable     = $menus->random();
                        $orderableType = 'App\\Models\\Menu';
                        break;

                    case 'custom_menu':
                        $orderable     = $customMenus->random();
                        $orderableType = 'App\\Models\\CustomMenu'; // <-- nouveau type
                        break;
                }

                OrderItem::create([
                    'order_id'       => $order->id,
                    'orderable_type' => $orderableType,   // morphMap prêt à l’emploi
                    'orderable_id'   => $orderable->id,
                    'quantity'       => rand(1, 3),
                    'price'          => $orderable->price, // CustomMenu::getPriceAttribute()
                ]);
            }
        }

        $this->command->info('OrderItems seeded successfully – custom menus included!');
    }
}
