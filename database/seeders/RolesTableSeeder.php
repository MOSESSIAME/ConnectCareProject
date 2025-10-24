<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'Super Admin',
            'Admin',
            'Pastor',
            'District Coordinator',
            'Zonal Leader',        // keep if you already used it in routes
            'Homecell Leader',
            'Team Leader',
            'Team Member',
            'Staff',
        ];

        foreach ($roles as $name) {
            $exists = DB::table('roles')->where('name', $name)->exists();
            if (!$exists) {
                DB::table('roles')->insert([
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
