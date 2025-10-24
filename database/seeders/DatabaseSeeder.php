<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,          // must run first
            ServiceUnitsTableSeeder::class,   // preload service units
            UsersTableSeeder::class,          // create Super Admin
            ZonesTableSeeder::class,       //  added this
            ServicesTableSeeder::class,    //  added this
        ]);
    }
}
