<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Order::factory(20)->create()->each(function ($order) {
            \App\Models\OrderItem::factory(3)->create(['order_id' => $order->id]);
        });
    }
}
