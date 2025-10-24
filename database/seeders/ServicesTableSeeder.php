<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesTableSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'First Service',  'service_date' => now()->toDateString()],
            ['name' => 'Second Service', 'service_date' => now()->toDateString()],
            ['name' => 'Third Service',  'service_date' => now()->toDateString()],
            ['name' => 'Midweek Service','service_date' => now()->toDateString()],
        ];

        foreach ($services as $service) {
            DB::table('services')->updateOrInsert(
                ['name' => $service['name'], 'service_date' => $service['service_date']],
                $service
            );
        }
    }
}
