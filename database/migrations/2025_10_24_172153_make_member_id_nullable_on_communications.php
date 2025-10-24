<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('communications', 'member_id')) {
            // Use raw SQL so we don’t need doctrine/dbal
            DB::statement('ALTER TABLE communications MODIFY member_id BIGINT UNSIGNED NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('communications', 'member_id')) {
            // Revert to NOT NULL if you really need to
            DB::statement('ALTER TABLE communications MODIFY member_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
