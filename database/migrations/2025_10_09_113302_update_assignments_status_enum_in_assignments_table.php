<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `assignments` MODIFY COLUMN `status` ENUM('Active','Reassigned','Completed') DEFAULT 'Active'");
    }

    public function down(): void
    {
        // Change this to whatever the previous definition was, if different.
        DB::statement("ALTER TABLE `assignments` MODIFY COLUMN `status` ENUM('Active','Reassigned','Completed') DEFAULT 'Active'");
    }
};
