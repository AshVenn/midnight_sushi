<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use App\Models\Setting; // <-- important if you want to fetch fee from settings
use Carbon\Carbon;

class DeliveriesSeeder extends Seeder
{
    public function run(): void
    {
        // If you only want to assign deliveries for orders currently 'pending':
        // $orders = Order::where('status', 'pending')->get();

        // Or, if you want deliveries for *all* orders:
        $orders = Order::all();

        // Use your existing scope: role('delivery_person')
        $deliveryPersons = User::role('delivery_person')->get();

        if ($orders->isEmpty()) {
            $this->command->warn('No orders found. Skipping DeliveriesSeeder.');
            return;
        }

        if ($deliveryPersons->isEmpty()) {
            // Optionally generate some new delivery persons
            User::factory(5)->create(['role' => 'delivery_person']);
            $deliveryPersons = User::role('delivery_person')->get();
        }

        $returnReasons = [
            'Client absent',
            'Adresse incorrecte',
            'Problème de paiement',
            'Autre raison',
        ];

        foreach ($orders as $order) {
            // Random final status for demonstration (assigned, picked_up, delivered, returned)
            $status = collect(['assigned', 'picked_up', 'delivered', 'returned'])->random();

            // Build timestamps
            $assignedAt = Carbon::now()->subDays(rand(1, 5));
            $pickedUpAt = $status !== 'assigned'
                ? $assignedAt->copy()->addHours(rand(1, 6))
                : null;
            $deliveredAt = $status === 'delivered'
                ? $pickedUpAt->copy()->addHours(rand(1, 6))
                : null;
            $returnedAt = $status === 'returned'
                ? $pickedUpAt->copy()->addHours(rand(1, 6))
                : null;

            Delivery::create([
                'order_id'           => $order->id,
                'delivery_person_id' => $deliveryPersons->random()->id,
                'status'             => $status,
                'assigned_at'        => $assignedAt,
                'picked_up_at'       => $pickedUpAt,
                'delivered_at'       => $deliveredAt,
                'returned_at'        => $returnedAt,
                'return_reason'      => $status === 'returned' ? collect($returnReasons)->random() : null,
                'address_text'       => $order->address_text,
                'phone_number'       => $order->phone_number,
                'notes'              => 'Please deliver ASAP.',
                'delivery_fee'       => Setting::getValue('delivery_fee', 2.50),
            ]);
        }

        $this->command->info('Deliveries seeded successfully!');
    }
}
