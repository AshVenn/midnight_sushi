<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Create core team
        $this->createCoreTeam();
        
        // Create 2 delivery persons
        $this->createDeliveryPersons();
        
        // Create 2 customers with addresses
        $this->createCustomers();
    }

    private function createCoreTeam()
    {
        User::factory()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@midnight-sushi.ch',
            'password' => Hash::make('secret'),
            'role' => 'admin',
            'avatar' => '/images/midnight_sushi/midnight_sushi_user.jpg'
        ]);

        User::factory()->create([
            'first_name' => 'Manager',
            'last_name' => 'User',
            'email' => 'manager@midnight-sushi.ch',
            'password' => Hash::make('secret'),
            'role' => 'manager',
            'avatar' => '/images/midnight_sushi/midnight_sushi_user.jpg'
        ]);
    }

    private function createDeliveryPersons()
    {
        User::factory()->count(2)->state([
            'role' => 'delivery_person',
            'avatar' => '/images/midnight_sushi/midnight_sushi_user.jpg',
            'password' => Hash::make('secret')
        ])->create();
    }

    private function createCustomers()
    {
        $customers = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@example.com',
                'phone_number' => '+41791234567'
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Curie',
                'email' => 'marie.curie@example.com',
                'phone_number' => '+41797654321'
            ]
        ];

        foreach ($customers as $customer) {
            User::factory()->create(array_merge($customer, [
                'role' => 'customer',
                'avatar' => '/images/midnight_sushi/midnight_sushi_user.jpg',
                'password' => Hash::make('secret')
            ]));
        }
    }
}