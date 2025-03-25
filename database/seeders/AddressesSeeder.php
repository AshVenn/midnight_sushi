<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Address;
use App\Models\User;

class AddressesSeeder extends Seeder
{
    public function run()
    {
        $customers = User::where('role', 'customer')->get();

        foreach ($customers as $customer) {
            Address::factory()->count(2)->create(['user_id' => $customer->id]);
        }
    }
}