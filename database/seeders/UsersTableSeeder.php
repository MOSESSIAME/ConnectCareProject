<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');

        // Seed a Super Admin account
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@connectcare.test'], // unique key
            [
                'name' => 'Super Admin',
                'phone' => '0000000000',
                'password' => Hash::make('Password@123'), // change after first login
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
