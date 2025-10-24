
<?php

// database/migrations/xxxx_xx_xx_xxxxxx_make_user_id_nullable_on_communications.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('communications', 'user_id')) {
            DB::statement('ALTER TABLE communications MODIFY user_id BIGINT UNSIGNED NULL');
        }
    }
    public function down(): void
    {
        if (Schema::hasColumn('communications', 'user_id')) {
            DB::statement('ALTER TABLE communications MODIFY user_id BIGINT UNSIGNED NOT NULL');
        }
    }
};

