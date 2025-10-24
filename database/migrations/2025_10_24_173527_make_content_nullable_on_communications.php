<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('communications', 'content')) {
            DB::statement('ALTER TABLE communications MODIFY content TEXT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('communications', 'content')) {
            DB::statement('ALTER TABLE communications MODIFY content TEXT NOT NULL');
        }
    }
};
