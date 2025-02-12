<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactInquiry;

class ContactInquiriesSeeder extends Seeder
{
    public function run(): void
    {
        ContactInquiry::factory(15)->create();
    }
}
