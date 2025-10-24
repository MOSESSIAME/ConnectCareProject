<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceUnit;

class ServiceUnitsTableSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'Choir', 'description' => 'Responsible for worship and music'],
            ['name' => 'Ushering', 'description' => 'Helps organize seating and church order'],
            ['name' => 'Media', 'description' => 'Handles sound, projection, and online media'],
            ['name' => 'Technical', 'description' => 'Responsible for equipment and systems'],
            ['name' => 'Evangelism', 'description' => 'Outreach and soul winning'],
        ];

        foreach ($units as $unit) {
            ServiceUnit::create($unit);
        }
    }
}
