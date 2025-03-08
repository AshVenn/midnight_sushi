<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Delivery;
use Carbon\Carbon;

class DeliveriesSeeder extends Seeder
{
    public function run(): void
    {
        $orders = Order::where('status', 'pending')->get();
        $deliveryPersons = User::where('role', 'delivery_person')->get();

        if ($orders->isEmpty()) {
            $this->command->warn('No pending orders found. Skipping DeliveriesSeeder.');
            return;
        }

        if ($deliveryPersons->isEmpty()) {
            User::factory(5)->create(['role' => 'delivery_person']);
            $deliveryPersons = User::where('role', 'delivery_person')->get();
        }

        $returnReasons = [
            'Client absent',
            'Adresse incorrecte',
            'Problème de paiement',
            'Autre raison'
        ];

        foreach ($orders as $order) {
            $status = collect(['assigned', 'picked_up', 'delivered', 'returned'])->random();

            $assignedAt = Carbon::now()->subDays(rand(1, 5));
            $pickedUpAt = $status !== 'assigned' ? $assignedAt->copy()->addHours(rand(1, 6)) : null;
            $deliveredAt = $status === 'delivered' ? $pickedUpAt->copy()->addHours(rand(1, 6)) : null;
            $returnedAt = $status === 'returned' ? $pickedUpAt->copy()->addHours(rand(1, 6)) : null;

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
                'phone_number'       => '555-1234',
                'notes'              => 'Please deliver ASAP.',
                'delivery_fee'       => 2.50,
            ]);
        }

        $this->command->info('Deliveries seeded successfully!');
    }
}
