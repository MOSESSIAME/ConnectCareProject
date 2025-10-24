<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add the column + FK only if the column does NOT exist yet
        if (! Schema::hasColumn('assignments', 'team_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                $table->foreignId('team_id')
                    ->nullable()
                    ->constrained('teams')
                    ->nullOnDelete()
                    ->after('assigned_to');
            });
        }

        // 2) Create indexes only if they don't already exist
        if (! $this->indexExists('assignments', 'assignments_team_id_index')) {
            DB::statement('CREATE INDEX assignments_team_id_index ON assignments(team_id)');
        }

        if (! $this->indexExists('assignments', 'assignments_assigned_to_status_index')) {
            DB::statement('CREATE INDEX assignments_assigned_to_status_index ON assignments(assigned_to, status)');
        }
    }

    public function down(): void
    {
        // Drop indexes if present
        if ($this->indexExists('assignments', 'assignments_assigned_to_status_index')) {
            DB::statement('DROP INDEX assignments_assigned_to_status_index ON assignments');
        }
        if ($this->indexExists('assignments', 'assignments_team_id_index')) {
            DB::statement('DROP INDEX assignments_team_id_index ON assignments');
        }

        // Drop FK + column if it exists
        if (Schema::hasColumn('assignments', 'team_id')) {
            Schema::table('assignments', function (Blueprint $table) {
                // dropConstrainedForeignId handles FK + column if it was created via constrained()
                $table->dropConstrainedForeignId('team_id');
            });
        }
    }

    // --- helpers ---
    private function indexExists(string $table, string $index): bool
    {
        $dbName = DB::getDatabaseName();
        $rows = DB::select(
            'SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$dbName, $table, $index]
        );
        return !empty($rows);
    }
};
