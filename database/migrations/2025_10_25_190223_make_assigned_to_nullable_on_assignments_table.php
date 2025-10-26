
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Adjust names if your FK is custom
        DB::statement('ALTER TABLE `assignments` DROP FOREIGN KEY `assignments_assigned_to_foreign`');
        DB::statement('ALTER TABLE `assignments` MODIFY `assigned_to` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `assignments` ADD CONSTRAINT `assignments_assigned_to_foreign`
                       FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `assignments` DROP FOREIGN KEY `assignments_assigned_to_foreign`');
        DB::statement('ALTER TABLE `assignments` MODIFY `assigned_to` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `assignments` ADD CONSTRAINT `assignments_assigned_to_foreign`
                       FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE RESTRICT');
    }
};
