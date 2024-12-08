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
                'role' => 'admin',
            ],
            [
                'first_name' => 'Manager',
                'last_name' => 'User',
                'email' => 'manager1@example.com',
                'password' => Hash::make('manager1@example.com'),
                'role' => 'manager',
            ],
            [
                'first_name' => 'Customer',
                'last_name' => 'User',
                'email' => 'customer1@example.com',
                'password' => Hash::make('customer1@example.com'),
                'role' => 'customer',
            ],
            [
                'first_name' => 'Delivery',
                'last_name' => 'Person',
                'email' => 'delivery1@example.com',
                'password' => Hash::make('delivery1@example.com'),
                'role' => 'delivery_person',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
