<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\User;
use App\Models\Setting;
use Carbon\Carbon;

class DeliveriesSeeder extends Seeder
{
    public function run(): void
    {
        $referenceDate = Carbon::create(2024, 1, 1, 0, 0, 0);
        $orders = Order::all();
        $deliveryPersons = User::role('delivery_person')->get();

        if ($orders->isEmpty()) {
            $this->command->warn('Aucune commande trouvée. DeliveriesSeeder ignoré.');
            return;
        }

        if ($deliveryPersons->isEmpty()) {
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
            $status = collect(['assigned', 'picked_up', 'delivered', 'returned'])->random();

            // Generate dates relative to reference date
            $assignedAt = $referenceDate->copy()->subDays(rand(1, 5));
            $pickedUpAt = $status !== 'assigned'
                ? $assignedAt->copy()->addHours(rand(1, 6))
                : null;

            $deliveredAt = ($status === 'delivered' && $pickedUpAt)
                ? $pickedUpAt->copy()->addHours(rand(1, 6))
                : null;

            $returnedAt = ($status === 'returned' && $pickedUpAt)
                ? $pickedUpAt->copy()->addHours(rand(1, 6))
                : null;

            Delivery::create([
                'order_id'           => $order->id,
                'delivery_person_id' => $deliveryPersons->random()->id,
                'status'             => $status,
                'assigned_at'        => $assignedAt->format('Y-m-d H:i:s'),
                'picked_up_at'       => $pickedUpAt instanceof Carbon ? $pickedUpAt->format('Y-m-d H:i:s') : null,
                'delivered_at'       => $deliveredAt instanceof Carbon ? $deliveredAt->format('Y-m-d H:i:s') : null,
                'returned_at'        => $returnedAt instanceof Carbon ? $returnedAt->format('Y-m-d H:i:s') : null,
                'return_reason'      => $status === 'returned' ? collect($returnReasons)->random() : null,
                'address_text'       => $order->address_text,
                'phone_number'       => $order->phone_number,
                'notes'              => 'Please deliver ASAP.',
                'delivery_fee'       => Setting::getValue('delivery_fee', 2.50),
            ]);
        }
    }
}
