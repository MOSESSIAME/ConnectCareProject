<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZonesTableSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            ['name' => 'Kabwata Zone', 'leader_id' => null],
            ['name' => 'Woodlands Zone', 'leader_id' => null],
            ['name' => 'Chilenje Zone', 'leader_id' => null],
        ];

        foreach ($zones as $zone) {
            DB::table('zones')->updateOrInsert(
                ['name' => $zone['name']],
                $zone
            );
        }
    }
}
