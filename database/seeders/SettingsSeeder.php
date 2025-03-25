<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        Setting::setValue('delivery_fee', 5.00);
    }
}
