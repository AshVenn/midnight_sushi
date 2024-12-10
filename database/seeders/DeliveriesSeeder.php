<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Order;
use App\Models\User;
use App\Models\Delivery;

class DeliveriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::where('status', 'pending')->get();
        $deliveryPersons = User::where('role', 'delivery_person')->get();

        foreach ($orders as $order) {
            Delivery::create([
                'order_id' => $order->id,
                'delivery_person_id' => $deliveryPersons->random()->id,
                'status' => 'assigned',
                'assigned_at' => now(),
            ]);
        }
    }
}
