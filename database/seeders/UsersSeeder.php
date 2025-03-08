<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin1@example.com',
                'password' => Hash::make('admin1@example.com'),
                'avatar' => '/images/midnight_sushi/midnight_sushi_user.jpg',
                'role' => 'admin',
            ],
            [
                'first_name' => 'Manager',
                'last_name' => 'User',
                'email' => 'manager1@example.com',
                'password' => Hash::make('manager1@example.com'),
                'avatar' => '/images/midnight_sushi/midnight_sushi_user.jpg',
                'role' => 'manager',
            ],
            [
                'first_name' => 'Customer',
                'last_name' => 'User',
                'email' => 'customer1@example.com',
                'password' => Hash::make('customer1@example.com'),
                'avatar' => '/images/midnight_sushi/midnight_sushi_user.jpg',
                'role' => 'customer',
            ],
            [
                'first_name' => 'Delivery',
                'last_name' => 'Person',
                'email' => 'delivery1@example.com',
                'password' => Hash::make('delivery1@example.com'),
                'avatar' => '/images/midnight_sushi/midnight_sushi_user.jpg',
                'role' => 'delivery_person',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        
        User::factory()->count(3)->state([
            'role' => 'delivery_person',
        ])->create();

       
        User::factory()->count(10)->state([
            'role' => 'customer',
        ])->create();
    }
}
