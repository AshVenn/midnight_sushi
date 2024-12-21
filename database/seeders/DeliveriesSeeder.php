<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Delivery;

class DeliveriesSeeder extends Seeder
{
    public function run(): void
    {
        // Get "pending" or other orders where delivery might be assigned
        $orders = Order::where('status', 'pending')->get();
        $deliveryPersons = User::where('role', 'delivery_person')->get();

        if ($orders->isEmpty()) {
            $this->command->warn('No pending orders found. Skipping DeliveriesSeeder.');
            return;
        }

        // If we have no delivery persons, create some
        if ($deliveryPersons->isEmpty()) {
            User::factory(5)->create(['role' => 'delivery_person']);
            $deliveryPersons = User::where('role', 'delivery_person')->get();
        }

        foreach ($orders as $order) {
            Delivery::create([
                'order_id'           => $order->id,
                'delivery_person_id' => $deliveryPersons->random()->id,
                'status'             => 'assigned',
                'assigned_at'        => now(),
                'address_text'       => $order->address_text,
                'phone_number'       => '555-1234',
                'notes'              => 'Please deliver ASAP.',
                'delivery_fee'       => 2.50,
            ]);
        }

        $this->command->info('Deliveries seeded successfully for pending orders!');
    }
}
